<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Router
 *
 * @author gabriel
 */
class Router
{
    protected $oDb;
    protected $oSession;
    protected $sXml;

    protected $oHandler;
    /** @var OperationRouterDTO $dto **/
    protected $oHandlerDTO;

    public function __construct(OperationRouterDTO $dto)
    {
//        $this->oDb      = $dto->oDb;
//        $this->oSession = $dto->oSession;
//        $this->sXml     = $dto->sXml;

        $this->oHandlerDTO = $dto;

        $this->oHandler = null;
    }

    public function handle($handler)
    {
        try {
//            $aXml = xml
            $oXml = simplexml_load_string($this->oHandlerDTO->sXml);

            if (!$oXml) {
                throw new Exception('WRONG_XML');
            }

            $sHandlerClassName = Config::getMethodHandler($handler);

            $this->oHandler = new $sHandlerClassName();

            $message = $this->oHandler->$handler($this->oHandlerDTO);
        }
        catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $message;
    }

    private function _xmlToArray()
    {
        $array = array();

        $parser = xml_parser_create();
        $obj = xml_parse($parser, $this->oHandlerDTO->sXml);
        $obj = xml_parse_into_struct($parser, $this->oHandlerDTO->sXml, $array);

        return array();
    }
}