<?php

namespace Tables;

/**
 * Provides an interface between a dataset and data-aware components.
 */
interface IDataSource extends Countable, IteratorAggregate
{
	//function IteratorAggregate::getIterator();
	//function Countable::count();
}

