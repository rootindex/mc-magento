<?php
/**
 * Created by Ebizmarts
 * Date: 1/18/18
 * Time: 3:49 PM
 */

class Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder $_block
     */
    private $_block;
    /**
     * @var \Mage_Sales_Model_Order $_orderMock
     */
    private $_orderMock;

    public function setUp()
    {
        $app = Mage::app('default');
        $layout = $app->getLayout();
        $this->_block = new Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder;
        $this->_orderMock = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getStoreId', 'getEntityId'))
            ->getMock();

        /* We are required to set layouts before we can do anything with blocks */
        $this->_block->setLayout($layout);
    }

    /**
     * @dataProvider renderDataProvider
     */

    public function testRender($status)
    {
        $orderId = 1;
        $mailchimpStoreId = '5axx998994cxxxx47e6b3b5dxxxx26e2';
        $storeId = 1;

        if ($status == array(1, 1)){
            $assertStatus = '<div style ="color:green">Yes</div>';
        } elseif ($status === array(null, 1)){
            $assertStatus = '<div style ="color:#ed6502">Processing</div>';
        } elseif ($status === array(null, null)){
           $assertStatus = '<div style ="color:mediumblue">In queue</div>';
        } else {
            $assertStatus = '<div style ="color:red">No</div>';
        }

        $orderMock = $this->_orderMock;

        $blockMock = $this->getMockBuilder(Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('makeHelper', 'makeApiOrders'))
            ->getMock();


        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMCStoreId'))
            ->getMock();

        $modelMock = $this->getMockBuilder(Ebizmarts_MailChimp_Model_Api_Orders::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getSyncedOrder'))
            ->getMock();

        $blockMock->expects($this->once())->method('makeHelper')->willReturn($helperMock);
        $blockMock->expects($this->once())->method('makeApiOrders')->willReturn($modelMock);

        $orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $orderMock->expects($this->once())->method('getEntityId')->willReturn($orderId);

        $helperMock->expects($this->once())->method('getMCStoreId')->with($storeId)->willReturn($mailchimpStoreId);

        $modelMock->expects($this->once())->method('getSyncedOrder')->with($orderId, $mailchimpStoreId)->willReturn($status);

        $result = $blockMock->render($orderMock);

        $this->assertEquals($assertStatus, $result);
    }

    public function renderDataProvider(){

        return array(
            array(array(1, 1)),
            array(array(null, 1)),
            array(array(null, null)),
            array(array(0, 1))
        );
    }

}
