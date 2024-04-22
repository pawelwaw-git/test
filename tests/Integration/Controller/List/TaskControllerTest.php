<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\List;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Proxy;

/**
 * @internal
 *
 * @coversNothing
 */
class TaskControllerTest extends WebTestCase
{
    // invalid request
    // list with filters (isDone) and (Date)

    public function testValidRequest(): void
    {
        // GIVEN
        $client = static::createClient();
        $task = $this->createTask();

        // WHEN
        $client->request(
            'GET',
            'tasks'
        );

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(
            200,
            $response->getStatusCode(),
            'task repeatable type =>'.$task->getGoal()->getRepeatable().' task type => '.$task->getGoal()->getType()."response\r\n"
        .$response->getContent()
        );
        $this->assertSame(
            json_encode([
                [
                    'id' => $task->getId(),
                    'date' => $task->getDate()->format('Y-m-d'),
                    'goal' => [
                        'id' => $task->getGoal()->getId(),
                        'name' => $task->getGoal()->getName(),
                        'description' => $task->getGoal()->getDescription(),
                        'priority' => $task->getGoal()->getPriority(),
                        'type' => $task->getGoal()->getType(),
                        'repeatable' => $task->getGoal()->getRepeatable(),
                        'active' => $task->getGoal()->isActive(),
                        'last_date_schedule' => $task->getGoal()->getLastDateSchedule(),
                        'possible_to_plan' => $task->getGoal()->isPossibleToPlan(),
                    ],
                    'is_done' => $task->isIsDone(),
                ],
            ], JSON_THROW_ON_ERROR),
            $response->getContent()
        );
    }

    /**
     * @throws \Exception
     */
    private function createTask(): Proxy|TaskCalendar
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);

        $task = TaskCalendarFactory::createOne([
            'Goal' => $goal,
            'isDone' => true,
        ]);

        $task->save();

        return $task;
    }
}