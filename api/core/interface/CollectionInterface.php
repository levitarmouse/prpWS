<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author gabriel
 */
interface CollectionInterface
{
    public function getAll(DTO $dto);

    public function getNext(DTO $dto);
}