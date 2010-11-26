<?php

interface IPageable {
  public function setOffset($offset);
  public function setLimit($limit);
  public function page($offset, $limit);
}
