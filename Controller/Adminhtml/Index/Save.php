<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Amage\Cms\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;


class Save extends \Magento\Backend\App\Action
{

    protected $_store;
    protected $uploaderFactory;
    protected $allowedExtensions = ['csv'];
    protected $csv;
    protected $fileId = 'file';
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;
    /**
     * @var Block\Converter
     */
    protected $ConverterToArray;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Store\Model\StoreManagerInterface $_store,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Amage\Cms\Model\Block\ConverterToArray $ConverterToArray,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
    ) {

        $this->uploaderFactory = $uploaderFactory;
        $this->csv = $csv;
        $this->store = $_store;
        $this->blockFactory = $blockFactory;
        $this->ConverterToArray = $ConverterToArray;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            
            if(@$_FILES['import_file']) {
                $file = $_FILES['import_file'];
                if ($data['import_type'] == 1) {
                    $this->getBlock($file);
                } elseif ($data['import_type'] == 2) {
                    $this->getPage($file);
                } else {
                    $this->messageManager->addError(__("Please Select Import Type"));
                    $this->_redirect('*/*/');
                }
            }else{
                $this->messageManager->addError(__("Please Upload CSV file"));
            }
            
            $this->_redirect('*/*/');
        }catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_redirect('*/*/');
        }
    }


    /**
     * @param array $data
     * @return \Magento\Cms\Model\Block
     */
    protected function saveCmsBlock($data)
    {
        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->getResource()->load($cmsBlock, $data['identifier']);
        if (!$cmsBlock->getData()) {
            $cmsBlock->setData($data);
        } else {
            $cmsBlock->addData($data);
        }
        $cmsBlock->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
        $cmsBlock->setIsActive(1);
        $cmsBlock->save();
        return $cmsBlock;
    }


    /**
     * 
     * @param array $data
     * @return void
     *
     */
    protected function getBlock($file){

        if($file['size'] && $file){
                if (!isset($file['tmp_name'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $rows = $this->csv->getData($file['tmp_name']);
                $header = array_shift($rows);
                foreach ($rows as $row) {
                    $data = [];
                    foreach ($row as $key => $value) {
                        $data[$header[$key]] = $value;
                    }
                    $row = $data;
                    $data = $this->ConverterToArray->convertRow($row);
                    $cmsBlock = $this->saveCmsBlock($data['block']);
                    $cmsBlock->unsetData();
                }
                $this->messageManager->addSuccess(__("Content Data Updated"));
            }else{
                $this->messageManager->addError(__("File is empty"));
            }
    }


    /**
     * 
     * @param array $data
     * @return void
     *
     */
    protected function getPage($file){

        if($file['size'] && $file){
                if (!isset($file['tmp_name'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $rows = $this->csv->getData($file['tmp_name']);
                $header = array_shift($rows);
                foreach ($rows as $row) {
                    $data = [];
                    foreach ($row as $key => $value) {
                        $data[$header[$key]] = $value;
                    }
                    $row = $data;
                    $this->pageFactory->create()
                            ->load($row['identifier'], 'identifier')
                            ->addData($row)
                            ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                            ->save();
                }
                $this->messageManager->addSuccess(__("Content Data Updated"));
            }else{
                $this->messageManager->addError(__("File is empty"));
            }
    }
}