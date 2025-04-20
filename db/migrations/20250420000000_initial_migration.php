<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Создаем таблицу вопросов с явным определением первичного ключа
        $questions = $this->table('questions', ['id' => false, 'primary_key' => 'id']);
        $questions->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'null' => false])
                 ->addColumn('text', 'string', ['limit' => 255, 'null' => false])
                 ->addColumn('active', 'boolean', ['default' => true])
                 ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->create();

        // Создаем таблицу вариантов ответов
        $answerOptions = $this->table('answer_options');
        $answerOptions->addColumn('value', 'integer', ['null' => false])
                     ->addColumn('text', 'string', ['limit' => 100, 'null' => false])
                     ->create();

        // Создаем таблицу тестов
        $tests = $this->table('tests', ['id' => false, 'primary_key' => 'id']);
        $tests->addColumn('id', 'string', ['limit' => 16, 'null' => false])
              ->addColumn('creator_completed', 'boolean', ['default' => true])
              ->addColumn('partner_completed', 'boolean', ['default' => false])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->create();

        // Создаем таблицу ответов пользователей
        $userAnswers = $this->table('user_answers');
        $userAnswers->addColumn('test_id', 'string', ['limit' => 16, 'null' => false])
                   ->addColumn('question_id', 'integer', ['signed' => false, 'null' => false]) // Используем тот же тип, что и в questions
                   ->addColumn('answer_value', 'integer', ['null' => false])
                   ->addColumn('is_partner', 'boolean', ['default' => false])
                   ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                   ->addForeignKey('test_id', 'tests', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                   ->addForeignKey('question_id', 'questions', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                   ->create();

        // Добавляем начальные данные для вопросов
        $this->table('questions')->insert([
            ['text' => 'Тестовый вопрос номер 1', 'active' => true],
            ['text' => 'Тестовый вопрос номер 2', 'active' => true],
            ['text' => 'Тестовый вопрос номер 3', 'active' => true],
            ['text' => 'Тестовый вопрос номер 4', 'active' => true],
            ['text' => 'Тестовый вопрос номер 5', 'active' => true]
        ])->saveData();

        // Добавляем начальные данные для вариантов ответов
        $this->table('answer_options')->insert([
            ['value' => 1, 'text' => 'Фу'],
            ['value' => 2, 'text' => 'Нет'],
            ['value' => 3, 'text' => 'Если партнер хочет'],
            ['value' => 4, 'text' => 'Да'],
            ['value' => 5, 'text' => 'Конечно да']
        ])->saveData();
    }
}