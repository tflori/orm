<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Event;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function fetchedOnNewFromDatabase()
    {
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Fetched && $event->entity instanceof Article;
        })->once()->andReturnTrue();

        new Article(['id' => 23, 'title' => 'Foo Bar'], $this->em, true); // note the 3rd parameter
    }

    /** @test */
    public function changedOnDataChangeThroughSetAttribute()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar']);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Changed && $event->entity instanceof Article;
        })->once()->andReturnTrue();

        $article->setAttribute('title', 'New Title');
    }

    /** @test */
    public function savingCanBeCanceled()
    {
        $article = new Article(['title' => 'Foo Bar']);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saving && $event->entity instanceof Article;
        })->once()->andReturnFalse();
        $this->em->shouldNotReceive('insert');

        $article->save();
    }

    /** @test */
    public function insertingCanBeCanceled()
    {
        $article = new Article(['title' => 'Foo Bar']);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saving && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Inserting && $event->entity instanceof Article;
        })->once()->andReturnFalse();
        $this->em->shouldNotReceive('insert');

        $article->save();
    }

    /** @test */
    public function updatingCanBeCanceled()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar'], $this->em, true);
        $this->em->map($article);
        $article->title = 'New Title'; // now it is dirty

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saving && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('sync')->with($article)->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Updating && $event->entity instanceof Article;
        })->once()->andReturnFalse();
        $this->em->shouldNotReceive('update');

        $article->save();
    }

    /** @test */
    public function deletingCanBeCanceled()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar'], $this->em, true);
        $this->em->map($article);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Deleting && $event->entity instanceof Article;
        })->once()->andReturnFalse();
        $this->mocks['dbal']->shouldNotReceive('delete');

        $this->em->delete($article);
    }

    /** @test */
    public function insertedAndSavedAfterInsert()
    {
        $article = new Article(['title' => 'Foo Bar']);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saving && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Inserting && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('insert')->with($article, true)->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Inserted && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saved && $event->entity instanceof Article;
        })->once()->andReturnTrue();

        $article->save();
    }

    /** @test */
    public function updatedAndSavedAfterUpdate()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar'], $this->em, true);
        $this->em->map($article);
        $article->title = 'New Title'; // now it is dirty

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saving && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('sync')->with($article)->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Updating && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('update')->with($article)->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Updated && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Saved && $event->entity instanceof Article;
        })->once()->andReturnTrue();

        $article->save();
    }

    /** @test */
    public function deletedAfterDelete()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar'], $this->em, true);
        $this->em->map($article);

        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Deleting && $event->entity instanceof Article;
        })->once()->andReturnTrue();
        $this->mocks['dbal']->shouldReceive('deleteEntity')->with($article)->once()->andReturnTrue();
        $this->em->shouldReceive('fire')->withArgs(function (Event $event) {
            return $event instanceof Event\Deleted && $event->entity instanceof Article;
        })->once()->andReturnTrue();

        $this->em->delete($article);
    }
}
