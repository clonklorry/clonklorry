<?php

namespace Lorry\Model;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

abstract class AbstractModel implements NotifyPropertyChanged, Model
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id = null;

    /**
     * @var array
     */
    private $_listeners = array();

    /**
     * @return integer
     */
    final public function getId()
    {
        return $this->id;
    }

    final public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->_listeners[] = $listener;
    }

    final protected function _onPropertyChanged($propName, $oldValue, $newValue)
    {
        foreach ($this->_listeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }
}
