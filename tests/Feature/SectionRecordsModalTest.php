<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Livewire\Livewire;
use App\Livewire\Admin\Student\StudentRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionRecordsModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_records_modal_with_multi_activity_cards()
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'Admin User',
            'email'     => 'admin@agrico.com',
            'password'  => bcrypt('password'),
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(StudentRecord::class)
            ->call('openSectionRecordsModal')
            ->assertSet('showSectionRecordsModal', true)
            ->assertSee('Activity #1')
            ->assertSee('Add Another Activity')
            ->assertSee('Column Name (Scan)')
            ->call('addActivityCard')
            ->assertSee('Activity #2')
            ->set('activityCards.0.scan_name', 'MIDTERM FIELD LAB')
            ->assertSee('MIDTERM FIELD LAB')
            ->call('removeActivityCard', 1)
            ->assertDontSee('Activity #2');
    }

    public function test_section_records_table_renders_student_activity_columns()
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-002',
            'full_name' => 'Admin Super',
            'email'     => 'admin@agrico.com',
            'password'  => bcrypt('password'),
        ]);

        $student = User::create([
            'user_id' => 'STD-001',
            'student_id' => '2026-0001',
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@student.agrico.test',
            'password' => bcrypt('password'),
            'role' => 'student',
            'section' => 'BSIT 4A',
            'status' => 'Active',
            'student_record_status' => 'Active',
            'last_admin_id' => 'ADM-002',
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(StudentRecord::class)
            ->call('openSectionRecordsModal', 'BSIT 4A')
            ->assertSee('Juan Dela Cruz')
            ->assertSee('BSIT 4A')
            ->assertSee('SCAN SOIL ACTIVITY')
            ->assertSee('PIN LOCATION ACTIVITY')
            ->assertSee('xl-wrap')
            ->assertSee('xl-table')
            ->assertSee('student found');
    }

    public function test_section_records_modal_column_name_and_time_filters()
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-003',
            'full_name' => 'Admin Three',
            'email'     => 'admin3@agrico.com',
            'password'  => bcrypt('password'),
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(StudentRecord::class)
            ->call('openSectionRecordsModal')
            ->set('activityCards.0.scan_name', 'CUSTOM FIELD WORK')
            ->set('activityCards.0.start_time', '08:00')
            ->set('activityCards.0.end_time', '12:00')
            ->assertSee('CUSTOM FIELD WORK')
            ->assertSee('Column Name (Scan)')
            ->assertSee('Start Time')
            ->assertSee('End Time')
            ->assertSee('8:00 AM – 12:00 PM');
    }
}
