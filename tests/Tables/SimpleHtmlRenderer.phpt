<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */
 
require_once __DIR__ . '/../bootstrap.php';

$ds = Juz\Tables\DataSource\DoctrineRepositorySource::create(null, 'APIP');
$def = new Juz\Tables\Definition\DummyDefinition;

$renderer = new Juz\Tables\Creator\SimpleHtmlRenderer;
$renderer->setTableDefinition($def);
$renderer->setDataSource($ds);

//echo $renderer->toString();

// Test output
Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));

