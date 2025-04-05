<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\GradeTask;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

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
        
        // Ambil daftar task_name unik dari database
        $taskNames = GradeTask::distinct()->pluck('task_name');
        
        // Ambil daftar mata pelajaran unik dari tabel subjects
        $subjects = Subject::all();
        
        // Buat query builder untuk siswa
        $query = Student::query();
        
        // Tambahkan kondisi berdasarkan role
        if ($user->role_id != 1) {
            if (!$user->class_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas tertentu.');
            }
            $query->where('class_id', $user->class_id);
        }
        
        // Load relation gradeTasks dengan kondisi filter
        $query->with(['gradeTasks' => function ($query) use ($subject_id, $task_name) {
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }
            if ($task_name) {
                $query->where('task_name', $task_name);
            }
        }]);
        
        // Load relation notifications dengan kondisi filter
        $query->with(['notifications' => function($query) use ($subject_id, $task_name) {
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }
            if ($task_name) {
                $query->where('task_name', $task_name);
            }
        }]);
        
        // Pagination dengan 15 item per halaman
        $students = $query->paginate(15);

        // Get the active subject name if it exists
        $activeSubjectName = null;
        if ($subject_id) {
            $activeSubject = $subjects->firstWhere('id', $subject_id);
            $activeSubjectName = $activeSubject ? $activeSubject->name : null;
        }

        return view('notifications.index', compact(
            'students', 
            'subject_id', 
            'task_name', 
            'taskNames', 
            'subjects',
            'activeSubjectName'
        ));
    }

    public function sendNotification(Request $request)
    {
        try {
            $subject_id = $request->input('subject_id');
            $task_name = $request->input('task_name');
            $student_ids = $request->input('students', []);
            
            if (empty($student_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students selected'
                ], 400);
            }
        
            $students = Student::whereIn('id', $student_ids)
                ->with(['gradeTasks' => function ($query) use ($subject_id, $task_name) {
                    if ($subject_id) {
                        $query->where('subject_id', $subject_id);
                    }
                    if ($task_name) {
                        $query->where('task_name', $task_name);
                    }
                }])->get();
        
            $sent_count = 0;
            $failed_count = 0;
            
            foreach ($students as $student) {
                $task = $student->gradeTasks->first();
                if ($task) {
                    try {
                        // Base message
                        $message = "📢 Halo, berikut adalah nilai terbaru untuk {$student->name}:\n"
                                . "📖 Mata Pelajaran: {$task->subject->name}\n"
                                . "📝 Tugas: {$task->task_name}\n"
                                . "🎯 Nilai: {$task->score}\n\n";
                        
                        // Tambahkan pesan berdasarkan nilai
                        $score = (float) $task->score;
                        
                        if ($score < 60) {
                            $message .= "⚠️ Nilai masih di bawah KKM. Mohon bimbingan untuk meningkatkan pemahaman pada materi ini.";
                        } elseif ($score >= 60 && $score < 80) {
                            $message .= "👍 Nilai sudah cukup baik. Dengan sedikit usaha lebih, nilai dapat ditingkatkan.";
                        } else { // $score >= 80
                            $message .= "🌟 Nilai sangat baik! Pertahankan prestasi ini.";
                        }
                        
                        // Kirim pesan
                        $sendResult = $this->fonnteService->sendMessage($student->parent_phone, $message);
                        
                        // Verifikasi hasil pengiriman pesan
                        if ($sendResult) {
                            // Simpan status pengiriman ke database
                          // Di method sendNotification
                        $notification = Notification::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $task->subject_id,
                                'task_name' => $task->task_name,
                            ],
                            [
                                'sent_at' => now(),
                            ]
                        );
                            Log::info("Notification sent and saved for student {$student->id}: {$notification->id}");
                            $sent_count++;
                        } else {
                            Log::error("Failed to send notification to {$student->parent_phone}");
                            $failed_count++;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error sending notification for student {$student->id}: " . $e->getMessage());
                        $failed_count++;
                    }
                }
            }
        
            return response()->json([
                'success' => true,
                'message' => "Successfully sent {$sent_count} notifications. Failed: {$failed_count}",
                'refresh' => true
            ]);
        } catch (\Exception $e) {
            Log::error("Error in sendNotification method: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function resetNotificationStatus(Request $request)
    {
        try {
            $subject_id = $request->input('subject_id');
            $task_name = $request->input('task_name');
            $student_ids = $request->input('students', []);
            
            $query = Notification::query();
            
            // Filter berdasarkan siswa jika ada
            if (!empty($student_ids)) {
                $query->whereIn('student_id', $student_ids);
            }
            
            // Filter berdasarkan subject_id dan task_name jika ada
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }
            
            if ($task_name) {
                $query->where('task_name', $task_name);
            }
            
            // Set sent_at ke null untuk menandai belum terkirim
            $updated = $query->update(['sent_at' => null]);
            
            Log::info("Reset {$updated} notification statuses");
            
            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman berhasil direset',
                'updated_count' => $updated,
                'refresh' => true
            ]);
        } catch (\Exception $e) {
            Log::error("Error in resetNotificationStatus: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
