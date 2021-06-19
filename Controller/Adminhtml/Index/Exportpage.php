<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Amage\Cms\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Exportpage extends \Magento\Backend\App\Action
{

    protected $uploaderFactory; 

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Cms\Api\PageRepositoryInterface $blockRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
        

    ) {
       parent::__construct($context);
       $this->_fileFactory = $fileFactory;
       $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR); // VAR Directory Path
       $this->blockRepository = $blockRepository;
       $this->searchCriteriaBuilder = $searchCriteriaBuilder;
       parent::__construct($context);
    }

     public function execute()
    {   


        $searchCriteria = $this->searchCriteriaBuilder->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();


        $name = date('m-d-Y-H-i-s');
        $filepath = 'export/page-data-' .$name. '.csv'; // at Directory path Create a Folder Export and FIle
        $this->directory->create('export');

        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        $columns = ['title','page_layout','meta_keyword','meta_description','identifier','content_heading','content','store_id', 'is_active'];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }

         $stream->writeCsv($header);

         foreach($cmsBlocks as $item){
            $itemData = [];
            $itemData[] = $item->getTitle();
            $itemData[] = $item->getPageLayout();
            $itemData[] = $item->getMetaKeywords();
            $itemData[] = $item->getMetaDescription();
            $itemData[] = $item->getIdentifier();
            $itemData[] = $item->getContentHeading();
            $itemData[] = $item->getContent();
            $itemData[] = implode(",",$item->getStoreId());
            $itemData[] = $item->getIsActive();
            $stream->writeCsv($itemData);

         }

        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder

        $csvfilename = 'pages-data-'.$name.'.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);

    }
}