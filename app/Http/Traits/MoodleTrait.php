<?php

namespace App\Http\Traits;

use App\Exports\ReportsExport;
use Exception;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

trait MoodleTrait
{
    use DataTrait;

    public function canMoodleLogin(): bool
    {
        return (bool)$this->getData(['moodle_login'])['moodle_login'][0];
    }

    public function canMoodleRegistration(): bool
    {
        return (bool)$this->getData(['moodle_registration'])['moodle_registration'][0];
    }

    public function uploadMoodleStudent($username): string
    {
        if ($this->canMoodleRegistration()) {
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            $student = $this->getStudentInfo($username);
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'username'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'password'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'firstname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'lastname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'email'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'auth'
                    ]
                ]
            ];
            $courses = $this->getRegisteredCourses($student['username'], $year, $semester);
            $export_data = [];
            $export_data[0][] = strtolower($student['username']);
            $export_data[0][] = $student['password'];
            $export_data[0][] = $student['username'];
            $export_data[0][] = $student['name'];
            $export_data[0][] = $student['email'] ?: $student['username'] . '@gmail.com';
            $export_data[0][] = 'none';
            for ($i = 0; $i < count($courses); $i++) {
                $headers[0][] = [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'course' . ($i + 1)
                ];
                $headers[0][] = [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'role' . ($i + 1)
                ];
                $export_data[0][] = $courses[$i]['full_code'];
                $export_data[0][] = 'student';
            }
            try {
                $csv = Excel::download(new ReportsExport([], $headers, $export_data), 'user.csv');
                $resposne = Http::attach('csv', file_get_contents($csv->getFile()), 'user.csv')
                    ->post('https://hitis-platform.ahi-egypt.net/admin/tool/uploaduser/external_upload_user.php');
                $string = strip_tags($resposne->body());
                $enrol_count = substr_count($string, 'Enrolled');
                $student_count = substr_count($string, 'student');
                // if (!($enrol_count == $student_count and $enrol_count == count($courses)) and
                //     str_contains($string, '{"user":true}')) {
                //     abort(500);
                // }
                return 'success';
            } catch (Exception $e) {
                //dd($e);
                return 'error';
            }
        }
        return 'cancel';
    }

  public function uploadMoodleBookStudent($username): string
    {
        if ($this->canMoodleRegistration()) {
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            $student = $this->getStudentInfo($username);
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'username'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'password'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'firstname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'lastname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'email'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'auth'
                    ]
                ]
            ];
            $courses = $this->getRegisteredCourses($student['username'], $year, $semester);
            $export_data = [];
            $export_data[0][] = strtolower($student['username']);
            $export_data[0][] = $student['password'];
            $export_data[0][] = $student['username'];
            $export_data[0][] = $student['name'];
            $export_data[0][] = $student['email'] ?: $student['username'] . '@gmail.com';
            $export_data[0][] = 'none';
            for ($i = 0; $i < count($courses); $i++) {
                $headers[0][] = [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'course' . ($i + 1)
                ];
                $headers[0][] = [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'role' . ($i + 1)
                ];
                $export_data[0][] = $courses[$i]['full_code'];
                $export_data[0][] = 'student';
            }
            try {
                $csv = Excel::download(new ReportsExport([], $headers, $export_data), 'user.csv');
                $resposne = Http::attach('csv', file_get_contents($csv->getFile()), 'user.csv')
                    ->post('https://hitis.egy-x.com/admin/tool/uploaduser/external_upload_user.php');
                $string = strip_tags($resposne->body());
                $enrol_count = substr_count($string, 'Enrolled');
                $student_count = substr_count($string, 'student');
                if (!($enrol_count == $student_count and $enrol_count == count($courses)) and
                    str_contains($string, '{"user":true}')) {
                    abort(500);
                }
                return 'success';
            } catch (Exception $e) {
                // dd($e);
                return 'error';
            }
        }
        return 'cancel';
    }
    public function updateMoodlePassword($username, $password): string
    {
        if ($this->canMoodleRegistration()) {
            $student = $this->getStudentInfo($username);
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'username'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'password'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'firstname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'lastname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'email'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'auth'
                    ]
                ]
            ];
            $export_data = [];
            $export_data[0][] = strtolower($student['username']);
            $export_data[0][] = $password;
            $export_data[0][] = $student['username'];
            $export_data[0][] = $student['name'];
            $export_data[0][] = $student['email'] ?: $student['username'] . '@gmail.com';
            $export_data[0][] = 'none';
            try {
                $csv = Excel::download(new ReportsExport([], $headers, $export_data), 'user.csv');
                $resposne = Http::attach('csv', file_get_contents($csv->getFile()), 'user.csv')
                    ->post('https://hitis-platform.ahi-egypt.net/admin/tool/uploaduser/external_upload_user.php');
                $string = strip_tags($resposne->body());
                if (str_contains($string, 'Error')) {
                    return 'error';
                }
                return 'success';
            } catch (Exception $e) {
                return 'error';
            }
        }
        return 'cancel';
    }


     public function updateMoodleBookPassword($username, $password): string
    {
        if ($this->canMoodleRegistration()) {
            $student = $this->getStudentInfo($username);
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'username'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'password'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'firstname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'lastname'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'email'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'auth'
                    ]
                ]
            ];
            $export_data = [];
            $export_data[0][] = strtolower($student['username']);
            $export_data[0][] = $password;
            $export_data[0][] = $student['username'];
            $export_data[0][] = $student['name'];
            $export_data[0][] = $student['email'] ?: $student['username'] . '@gmail.com';
            $export_data[0][] = 'none';
            try {
                $csv = Excel::download(new ReportsExport([], $headers, $export_data), 'user.csv');
                $resposne = Http::attach('csv', file_get_contents($csv->getFile()), 'user.csv')
                    ->post('https://hitis.egy-x.com/admin/tool/uploaduser/external_upload_user.php');
                $string = strip_tags($resposne->body());
                if (str_contains($string, 'Error')) {
                    return 'error';
                }
                return 'success';
            } catch (Exception $e) {
                return 'error';
            }
        }
        return 'cancel';
    }
}
