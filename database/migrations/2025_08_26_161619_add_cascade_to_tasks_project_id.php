<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel tasks ada
        if (Schema::hasTable('tasks')) {
            // 1. Nonaktifkan foreign key check sementara
            Schema::disableForeignKeyConstraints();

            // 2. Rename tabel lama
            Schema::rename('tasks', 'tasks_old');

            // 3. Buat tabel tasks baru dengan CASCADE
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->longText('description')->nullable();
                $table->string('image_path')->nullable();
                $table->string('status');
                $table->string('priority');
                $table->string('due_date')->nullable();
                $table->foreignId('assigned_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('project_id')
                      ->constrained('projects')
                      ->onDelete('cascade'); // Ini yang kita mau!
                $table->timestamps();
            });

            // 4. Pindahkan semua data dari tasks_old ke tasks
            DB::table('tasks_old')->orderBy('id')->chunk(100, function ($oldTasks) {
                foreach ($oldTasks as $task) {
                    DB::table('tasks')->insert([
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'image_path' => $task->image_path,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'due_date' => $task->due_date,
                        'assigned_user_id' => $task->assigned_user_id,
                        'created_by' => $task->created_by,
                        'updated_by' => $task->updated_by,
                        'project_id' => $task->project_id,
                        'created_at' => $task->created_at,
                        'updated_at' => $task->updated_at,
                    ]);
                }
            });

            // 5. Hapus tabel lama
            Schema::dropIfExists('tasks_old');

            // 6. Aktifkan kembali foreign key
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::rename('tasks', 'tasks_new');

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status');
            $table->string('priority');
            $table->string('due_date')->nullable();
            $table->foreignId('assigned_user_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('project_id')->constrained('projects');
            $table->timestamps();
        });

        DB::table('tasks_new')->chunk(100, function ($tasks) {
            foreach ($tasks as $task) {
                DB::table('tasks')->insert((array) $task);
            }
        });

        Schema::dropIfExists('tasks_new');

        Schema::enableForeignKeyConstraints();
    }
};