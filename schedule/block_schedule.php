<?php

class block_schedule extends block_base {

    public function init() {
        $this->title = get_string('schedule', 'block_schedule');
    }

    public function get_content() {
        global $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         = new stdClass;
        $this->content->text   = '';
        $this->content->footer = '';

        // Реализация алгоритма составления расписания
        $schedule = array(
            'Понедельник'    => array('class 1', 'class 2', 'class 3'),
            'Вторник'   => array('class 2', 'class 4', 'class 5'),
            'Среда' => array('class 1', 'class 3', 'class 5'),
            'Четверг'  => array('class 2', 'class 3', 'class 4'),
            'Пятница'    => array('class 1', 'class 4', 'class 5'),
            'Суббота'    => array('class 1', 'class 4', 'class 5')
        );

        $days = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница',  'Суббота');

        //require_once(dirname(__FILE__) . '/../../config.php'); // Подключаем конфигурационный файл Moodle

        //Получение наименований курсов
        $courses_name = array();
        $query = "SELECT id, fullname, shortname from {course}";
        $courselist = $DB->get_records_sql($query);
        foreach ($courselist as $course) {
            $courses_name[] = $course->fullname;
        }

        //Время
        $times = array('9:30 - 10:50', '11:00 - 12:20', '12:30 - 13:50', ' 2000', '0', '0', '0');


        //Получение преподавателей
        /*$courseid = 2; // ID курса
        $teachers = get_users_by_capability($courseid, 'moodle/course:update');
        $teachers_name = array();
        foreach ($teachers as $teacher) {
            $teachers_name[] = $teacher->firstname . ' ' . $teacher->lastname;
        }*/


        $contex_id_course_teacher = 2;
        $teachers_name = array();
        $teachers = $DB->get_records_sql('select id, userid, contextid from {role_assignments} where roleid = 3 OR roleid = 4;');
        $users = $DB->get_records_sql('select id, firstname, lastname from mdl_user;');
        foreach($users as $user) {
            foreach($teachers as $teacher) {
                if($user->id == $teacher->userid && $teacher->contextid == $contex_id_course_teacher) {
                    $teachers_name[] = (string)$user->firstname . ' ' . (string)$user->lastname;
                }
            }
        }


        // Формируем HTML-код для отображения расписания
        $html = '<table>';
        foreach($days as $day) {
            $i = 0;
            $html .= '<p align="center"><b>' . $day. '</b></p>';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td><b>' . 'Время' . '</b></td>';
            $html .= '<td><b>' . 'Дисциплина' . '</b></td>';
            $html .= '<td><b>' . 'Преподаватели' . '</b></td>'; 
            foreach($courses_name as $course) {
                $html .= '<tr>';
                $html .= '<td>' . $times[$i] . '</td>';
                $html .= '<td>' . $course . '</td>';
                $html .= '<td>' . 'teacher' . '</td>';
                $html .= '</tr>';
                ++$i;
            }
            $html .= '</table>';
        }
        $html .= '</table>';

        /*foreach ($schedule as $day => $classes) {
            $html .= '<p align="center">' . $day. '</p>';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td>' . implode('</td><td>', $classes) . '</td>';
            $html .= '</tr>';
            $html .= '</table>';
        }*/

        $this->content->text = $html;

        return $this->content;
    }

}