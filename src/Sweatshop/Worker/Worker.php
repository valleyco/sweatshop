<?php

namespace Sweatshop\Worker;

use Monolog\Logger;
use Pimple\Container;
use Sweatshop\Interfaces\MessageableInterface;
use Sweatshop\Message\Message;
use Sweatshop\Sweatshop;

abstract class Worker implements MessageableInterface
{
    protected $_di;

    public function __construct(Sweatshop $sweatshop)
    {
        $this->setDependencies($sweatshop->getDependencies());
        $this->tearUp();
    }

    public function __destruct()
    {
        $this->getLogger()->debug(sprintf('Worker "%s": tearing down', get_class($this)));
        $this->tearDown();
    }

    protected function tearDown()
    {
    }

    public function setDependencies(Container $di)
    {
        $this->_di = $di;
    }

    public function configure($config = [])
    {
    }

    public function execute(Message $message)
    {
        $this->getLogger()->debug(sprintf('Worker "%s" executing message "%s"', get_class($this), $message->getId()));

        return $this->work($message);
    }

    public function pushMessage(Message $message)
    {
        return $this->execute($message);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->_di['logger'];
    }

    protected function tearUp()
    {
        $this->getLogger()->info(sprintf('Worker "%s": tearing up', get_class($this)));
    }

    abstract protected function work(Message $message);
}
