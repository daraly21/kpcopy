<?php

namespace App\Http\Controllers;

use App\Models\GradeTask;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AcademicYear;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $subject_id = $request->input('subject_id');
        $task_name = $request->input('task_name');

        $subjects = Subject::all();

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif belum diset.');
        }

        $query = Student::query();

        if ($user->role_id != 1) {
            if (!$user->class_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas tertentu.');
            }
            
            // Ambil siswa yang terdaftar di kelas pada tahun ajaran aktif
            $studentIds = StudentClass::where('class_id', $user->class_id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('student_id');
            
            $query->whereIn('id', $studentIds);
        } else {
            // Admin: ambil semua siswa dari tahun ajaran aktif
            $studentIds = StudentClass::where('academic_year_id', $activeYear->id)
                ->pluck('student_id');
            
            $query->whereIn('id', $studentIds);
        }

        $query->with(['gradeTasks' => function ($query) use ($subject_id, $task_name) {
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }
            if ($task_name) {
                $query->where('task_name', $task_name);
            }
        }]);

        $query->with(['notifications' => function ($query) use ($subject_id, $task_name) {
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }
            if ($task_name) {
                $query->where('task_name', $task_name);
            }
        }]);

        $students = $query->paginate(15);

        // Ambil taskNames berdasarkan subject_id yang dipilih
        $taskNames = [];
        if ($subject_id) {
            $taskNames = GradeTask::where('subject_id', $subject_id)->distinct()->pluck('task_name');
        } else {
            $taskNames = GradeTask::distinct()->pluck('task_name');
        }

        $activeSubjectName = null;
        if ($subject_id) {
            $activeSubject = $subjects->firstWhere('id', $subject_id);
            $activeSubjectName = $activeSubject ? $activeSubject->name : null;
        }

        return view('notifications.index', compact('students', 'subject_id', 'task_name', 'taskNames', 'subjects', 'activeSubjectName'));
    }

    public function sendNotification(Request $request)
    {
        $subject_id = $request->input('subject_id');
        $task_name = $request->input('task_name');
        $student_ids = $request->input('students', []);

        if (empty($student_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada siswa yang dipilih',
            ], 400);
        }

        if (!$subject_id || !$task_name) {
            return response()->json([
                'success' => false,
                'message' => 'Subject ID dan Task Name harus diisi',
            ], 400);
        }

        // Cek apakah pengiriman dilarang karena belum 60 detik dari pengiriman terakhir
        $lastSentFile = 'last_notification_sent.txt';
        $currentTime = now()->timestamp;

        if (Storage::exists($lastSentFile)) {
            $lastSentTime = (int) Storage::get($lastSentFile);
            $timeDiff = $currentTime - $lastSentTime;

            if ($timeDiff < 60) {
                $remainingTime = 60 - $timeDiff;
                return response()->json([
                    'success' => false,
                    'message' => "Pengiriman dilarang! Harap tunggu $remainingTime detik sebelum mengirim notifikasi berikutnya untuk menghindari pemblokiran WhatsApp.",
                ], 429); // 429 Too Many Requests
            }
        }

        $students = Student::whereIn('id', $student_ids)
            ->with(['gradeTasks' => function ($query) use ($subject_id, $task_name) {
                $query->where('subject_id', $subject_id)->where('task_name', $task_name);
            }])
            ->get();

        $sent_count = 0;
        $failed_count = 0;

        foreach ($students as $student) {
            $task = $student->gradeTasks->first();
            if ($task) {
                $subject = Subject::find($task->subject_id);
                if (!$subject) {
                    Log::warning("Mata pelajaran dengan ID {$task->subject_id} tidak ditemukan untuk siswa ID {$student->id}");
                    $failed_count++;
                    continue;
                }

                // Template pesan baru dengan header SDN Cijedil
                $message = "🏫 *SDN Cijedil - Pemberitahuan Nilai Siswa* 🏫\n\n"
                         . "📢 Halo, berikut adalah nilai terbaru untuk *{$student->name}*:\n"
                         . "📖 *Mata Pelajaran*: {$subject->name}\n"
                         . "📝 *Tugas*: {$task->task_name}\n"
                         . "🎯 *Nilai*: {$task->score}\n\n";

                $score = (float) $task->score;

                if ($score < 60) {
                    $message .= "⚠️ *Peringatan*: Nilai masih di bawah KKM. Mohon bimbingan lebih lanjut untuk meningkatkan pemahaman materi.\n";
                } elseif ($score >= 60 && $score < 80) {
                    $message .= "👍 *Catatan*: Nilai sudah cukup baik. Tingkatkan sedikit lagi untuk hasil yang lebih maksimal!\n";
                } else {
                    $message .= "🌟 *Selamat*! Nilai sangat baik. Pertahankan prestasi ini, ya!\n";
                }

                $message .= "\n📌 Terima kasih atas perhatian Anda!";

                if ($student->parent_phone) {
                    $sendResult = $this->fonnteService->sendMessage($student->parent_phone, $message);
                    if ($sendResult) {
                        Notification::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $task->subject_id,
                                'task_name' => $task->task_name,
                            ],
                            ['sent_at' => now()]
                        );
                        $sent_count++;
                        Log::info("Notifikasi berhasil dikirim untuk siswa ID {$student->id} ke {$student->parent_phone}");

                        // Simpan timestamp pengiriman terakhir
                        Storage::put($lastSentFile, (string) now()->timestamp);
                    } else {
                        Log::error("Gagal mengirim notifikasi untuk siswa ID {$student->id} ke {$student->parent_phone}");
                        $failed_count++;
                    }
                } else {
                    Log::warning("Nomor orang tua tidak tersedia untuk siswa ID {$student->id}");
                    $failed_count++;
                }
            } else {
                Log::warning("Tidak ada tugas untuk siswa ID {$student->id}, subject_id {$subject_id}, task_name {$task_name}");
                $failed_count++;
            }
        }

        return response()->json([
            'success' => $sent_count > 0,
            'message' => "Berhasil mengirim {$sent_count} notifikasi. Gagal: {$failed_count}",
            'refresh' => true,
        ]);
    }

    public function resetNotificationStatus(Request $request)
    {
        try {
            $subject_id = $request->input('subject_id');
            $task_name = $request->input('task_name');
            $student_ids = $request->input('students', []);

            $query = Notification::query();

            if (!empty($student_ids)) {
                $query->whereIn('student_id', $student_ids);
            }

            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }

            if ($task_name) {
                $query->where('task_name', $task_name);
            }

            $updated = $query->update(['sent_at' => null]);

            Log::info("Reset {$updated} notification statuses");

            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman berhasil direset',
                'updated_count' => $updated,
                'refresh' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in resetNotificationStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}