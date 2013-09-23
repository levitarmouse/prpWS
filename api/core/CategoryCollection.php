<?php
/**
 * Subscriber class
 *
 * PHP version 5
 */

/**
 * Subscriber class
 */
class CategoryCollection extends MappedCollection
{
    protected $oMapper;

    public function __construct(CategoryDTO $dto)
    {
        parent::__construct($dto->oDb);
        $this->aObjCollection = array();

        $this->oMapper = CategoryCollectionMapper::getInstance($this->oDb);
    }

    public function getCollection(LoadByIdDTO $dto)
    {
        if ($dto->id != '') {
            $this->loadById($dto);
        }

        if (count($this->aRsCollection) > 0) {
            $oDTO = new CategoryDTO($this->oDb);
            foreach ($this->aRsCollection as $rs) {
                $obj = new Category($oDTO);
                $obj->initByResultSet($rs);
                $this->aObjCollection[] = $obj;
                unset($obj);
            }
        }

        return $this->aObjCollection;
    }
}