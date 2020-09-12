<?php

use ORM\Entity;
use ORM\EntityManager;
use ORM\Observer;

require_once 'bootstrap.php';
require_once 'entities.php';

/** @var EntityManager $em */

function out($message)
{
    echo (new DateTime)->format('[Y-m-d H:i:s.u] ') . $message . PHP_EOL;
}

// use a callback observer
$observer = User::observeBy()->on('fetched', function (User $user) {
    out(sprintf('Fetched User %s', $user->username));
})->on('fetched', function (User $user) {
    out(sprintf('Second listner called for %s', $user->name));
});

$query = User::query();
$query->one()->reset();

// stop listening for all fetched events
$observer->off('fetched');
$query->one();

// note that all listeners for 'fetched' are removed for this particular observer
$observer1 = User::observeBy()->on('fetched', function (User $user) {
    out(sprintf('Fetched User %s', $user->username));
});
$observer2 = User::observeBy()->on('fetched', function (User $user) {
    out(sprintf('Second listner called for %s', $user->name));
});

$query = User::query();
$query->one()->reset();

$observer2->off('fetched');
$query->one()->reset();

// disable whole observers
$observer = User::observeBy()->on('fetched', function (User $user) {
    out(sprintf('Fetched User %s', $user->username));
})->on('saving', function (User $user) {
    // disable saving for the User entity:
    out('Modification of users disabled');
    return false;
});

$query = User::query();
$user = $query->one();
$user->username = 'john.doe';
$user->save(); // not saved

$observer->off('saving');
$user->save(); // listener got removed

// other listener still active
$query->one();

// removes the observer itself
User::detachObserver($observer);
$query->one();

// both method exist for the entity manager too
$em->observe(User::class, $observer);
$em->detach($observer, User::class);

// It is also possible to assign observer to parent classes (also Entity itself)
// Check out the following example to get audit log of CRUD operations for all entities:
class AuditLog extends Observer
{
    protected function log($action, Entity $entity, array $dirty = null)
    {
        $name = $entity->name ?: get_class($entity) . ':' . implode('.', array_values($entity->getPrimaryKey()));
        $user = get_current_user();

        // we output this to console... you might want to store the information in a database
        out(sprintf('%s %s %s%s', $user, $action, $name, $dirty ? ' ' . json_encode($dirty) : ''));
    }

    public function fetched(Entity $entity)
    {
        $this->log('read', $entity);
    }

    public function inserted(Entity $entity, array $dirty = null)
    {
        $this->log('created', $entity, $entity->toArray());
    }

    public function updated(Entity $entity, array $dirty = null)
    {
        $this->log('updated', $entity, $dirty);
    }

    public function deleted(Entity $entity)
    {
        $this->log('deleted', $entity);
    }
}
$em->observe(Entity::class, new AuditLog());

$query = User::query();
$user1 = $query->one();
$user1->username = 'j.doe';
$user1->save();

$user1->save(); // note that it is not updated when nothing changed

$user = new User();
$user->username = 'jane.doe';
$user->password = md5('secret password');
$user->save(); // note that the password is not shown as it is excluded

$em->delete($user);
