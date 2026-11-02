<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [];
        foreach (
            [
                ['Sok Sreyneang', 'sreyneang@school.test', '012 111 222', 'Female', 'Mathematics'],
                ['Chea Vannak', 'vannak@school.test', '098 333 444', 'Male', 'Physics'],
                ['Keo Chantha', 'chantha@school.test', '017 555 666', 'Female', 'English'],
                ['Mao Rithy', 'rithy@school.test', '069 777 888', 'Male', 'History'],
                ['Pich Sopheary', 'sopheary@school.test', '085 999 000', 'Female', 'Computer Science'],
            ] as [$name, $email, $phone, $gender, $subject]
        ) {
            $teachers[] = Teacher::create(compact('name', 'email', 'phone', 'gender', 'subject'));
        }

        $classrooms = [];
        foreach (
            [
                ['Grade 10 A', 10, 0],
                ['Grade 10 B', 10, 1],
                ['Grade 11 A', 11, 2],
                ['Grade 12 A', 12, 4],
            ] as [$name, $grade, $t]
        ) {
            $classrooms[] = Classroom::create(['name' => $name, 'grade' => $grade, 'teacher_id' => $teachers[$t]->id]);
        }

        $students = [
            ['Kong Sopheak', 'kongsopheak@gmail.com', 'Male', '2008-03-14'],
            ['Ly Chhailin', 'lin@gmail.com', 'Female', '2008-07-02'],
            ['Pov Nita', 'povnita@gmail.com', 'Female', '2008-11-21'],
            ['Leng Makara', 'lengmakara@gmail.com', 'Male', '2008-01-30'],
            ['Sok Dara', 'sokdara@gmail.com', 'Male', '2007-05-09'],
            ['Chan Vireak', 'chanvireak@gmail.com', 'Male', '2007-09-17'],
            ['Heng Sophea', 'hengsophea@gmail.com', 'Female', '2007-12-25'],
            ['Srey Mom', 'sreymom@gmail.com', 'Female', '2006-04-11'],
            ['Tep Rotha', 'teprotha@gmail.com', 'Male', '2006-08-05'],
            ['Neang Bopha', 'neangbopha@gmail.com', 'Female', '2006-10-19'],
        ];

        foreach ($students as $i => [$name, $email, $gender, $dob]) {
            Student::create([
                'name' => $name,
                'email' => $email,
                'gender' => $gender,
                'dob' => $dob,
                'enrollment_date' => today()->subYears(rand(1, 3))->startOfYear()->addMonths(9),
                'classroom_id' => $classrooms[$i % 4]->id,
            ]);
        }
    }
}
