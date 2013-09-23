<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OperationRouter
 *
 * @author gabriel
 */
class OperationRouter extends Router
{
    public function __construct(OperationRouterDTO $dto)
    {
        parent::__construct($dto);
    }
}