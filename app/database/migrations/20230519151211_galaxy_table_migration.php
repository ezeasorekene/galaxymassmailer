<?php

use Phinx\Migration\AbstractMigration;

class GalaxyTableMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */

    public function change()
    {
        // Create Users table
        $users = $this->table('users', ['id' => 'user_id']);
        $users->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('email', 'string')
            ->addColumn('first_name', 'string')
            ->addColumn('last_name', 'string')
            ->addColumn('role', 'string')
            // Add more columns as needed
            ->addTimestamps()
            ->create();
        if ($this->isMigratingUp()) {
            $users->insert([
                    ['user_id' => 1, 'username' => 'ekene', 'password' => '123456', 'email' => 'ezeasorekene@gmail.com', 'first_name' => 'Ekene', 'last_name' => 'Ezeasor', 'role' => 'student']
                ])
                ->save();
        }            

        // Create Courses table
        $courses = $this->table('courses');
        $courses->addColumn('course_name', 'string')
            ->addColumn('instructor_id', 'integer')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('instructor_id', 'users', 'user_id')
            ->create();

        // Create Enrollments table
        $enrollments = $this->table('enrollments');
        $enrollments->addColumn('user_id', 'integer')
            ->addColumn('course_id', 'integer')
            // Add more columns as needed
            ->addForeignKey('user_id', 'users', 'user_id')
            ->addForeignKey('course_id', 'courses', 'id')
            ->create();

        // Create Assignments table
        $assignments = $this->table('assignments');
        $assignments->addColumn('course_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('assignment_name', 'string')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('course_id', 'courses', 'id')
            ->addForeignKey('user_id', 'users', 'user_id')
            ->create();

        // Create Submissions table
        $submissions = $this->table('submissions');
        $submissions->addColumn('assignment_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('submission_date', 'datetime', ['null' => true])
            ->addColumn('file_path', 'string')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('assignment_id', 'assignments', 'id')
            ->addForeignKey('user_id', 'users', 'user_id')
            ->create();

        // Create Quizzes table
        $quizzes = $this->table('quizzes');
        $quizzes->addColumn('course_id', 'integer')
            ->addColumn('quiz_name', 'string')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('course_id', 'courses', 'id')
            ->create();

        // Create QuizQuestions table
        $quizQuestions = $this->table('quiz_questions');
        $quizQuestions->addColumn('quiz_id', 'integer')
            ->addColumn('question_text', 'text')
            ->addColumn('option1', 'string')
            ->addColumn('option2', 'string')
            ->addColumn('option3', 'string')
            ->addColumn('option4', 'string')
            ->addColumn('correct_option', 'integer')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('quiz_id', 'quizzes', 'id')
            ->create();

        // Create QuizSubmissions table
        $quizSubmissions = $this->table('quiz_submissions');
        $quizSubmissions->addColumn('quiz_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('score', 'integer')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('quiz_id', 'quizzes', 'id')
            ->addForeignKey('user_id', 'users', 'user_id')
            ->create();

        // Create Grades table
        $grades = $this->table('grades');
        $grades->addColumn('user_id', 'integer')
            ->addColumn('course_id', 'integer')
            ->addColumn('assignment_id', 'integer', ['null' => true])
            ->addColumn('quiz_id', 'integer', ['null' => true])
            ->addColumn('score', 'integer')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('user_id', 'users', 'user_id')
            ->addForeignKey('course_id', 'courses', 'id')
            ->addForeignKey('assignment_id', 'assignments', 'id')
            ->addForeignKey('quiz_id', 'quizzes', 'id')
            ->create();

        // Create Discussions table
        $discussions = $this->table('discussions');
        $discussions->addColumn('user_id', 'integer')
            ->addColumn('course_id', 'integer')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('user_id', 'users', 'user_id')
            ->addForeignKey('course_id', 'courses', 'id')
            ->create();

        // Create Announcements table
        $announcements = $this->table('announcements');
        $announcements->addColumn('course_id', 'integer')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text')
            // Add more columns as needed
            ->addTimestamps()
            ->addForeignKey('course_id', 'courses', 'id')
            ->create();
    }

}
