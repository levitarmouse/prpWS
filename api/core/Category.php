<?php
/**
 * Subscriber class
 *
 * PHP version 5
 */

/**
 * Subscriber class
 */
class Category extends MappedEntity
{
    protected $oMapper;

    public function __construct(CategoryDTO $dto)
    {
        parent::__construct($dto->oDb);

        $categoryId = $dto->categoryId;
        $userId     = $dto->userId;
        $categoryName = $dto->categoryName;

        $this->oMapper = CategoryMapper::getInstance($this->oDb);

        if ($categoryId != '') {
            $this->loadByParams(new LoadByIdDTO($categoryId));
        }
        else if ($userId != '' && $categoryName != '') {
            $this->loadByName(new LoadByNameDTO($sName, $userId));
        }
    }

        public function getAll(DTO $dto)
    {

    }

    public function getNext(DTO $dto)
    {

    }

}