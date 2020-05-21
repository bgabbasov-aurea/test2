# add line
<?php
/**
* ###############################################
*
* Kayako Classic
* _______________________________________________
*
* @author        Werner Garcia <werner.garcia@crossover.com>
*
* @package       swift
* @copyright     Copyright (c) 2001-2018, Trilogy
* @license       http://kayako.com/license
* @link          http://kayako.com
*
* ###############################################
*/
// add line
namespace Tickets\Models\Ticket;

use Base\Models\User\SWIFT_User;
use Knowledgebase\Admin\LoaderMock;
use SWIFT_Exception;

/**
* Class TicketPostTest
* @group tickets
* add line
*/
class TicketPostTest extends \SWIFT_TestCase
{
    /**
     * @throws SWIFT_Exception
     */
    public function testConstructorReturnsClassInstance()
    {
        $this->getMockServices();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\SWIFT_Database $mockDb */
        $mockDb = $this->mockServices['Database'];
        $mockDb->method('QueryFetch')->willReturn([
            'ticketpostid' => 1,
        ]);
        $data = new \SWIFT_DataStore([
            'ticketpostid' => 1,
        ]);
        $obj = $this->getMockObject('Tickets\Models\Ticket\SWIFT_TicketPostMock', [
            'Data' => $data,
        ]);
        $this->assertInstanceOf('Tickets\Models\Ticket\SWIFT_TicketPost', $obj);
    }

    /**
     * @dataProvider getParsedContentsProvider
     * @param $_contents
     * @param $_settingValue
     * @param $_isContentHTML
     * @param $_overrideAllowableTags
     * @param $expected
     * @throws SWIFT_Exception
     */
    public function testGetParsedContents($_contents, $_settingValue, $_isContentHTML, $_overrideAllowableTags, $expected)
    {
        $settings = $this->createMock(\SWIFT_Settings::class);
        $settings->method('GetBool')
            ->willReturn(true);
        \SWIFT::GetInstance()->Settings = $settings;

        $actual = SWIFT_TicketPost::GetParsedContents($_contents, $_settingValue, $_isContentHTML, $_overrideAllowableTags);
        $this->assertEquals($expected, $actual);
    }

    public function getParsedContentsProvider()
    {
        return [
            ["<iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"></iframe>", "strip", true, '', ''],
            ["<iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"></iframe>", "entities", true, '', ''],
            ["<iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"></iframe>", "html", true, '', ''],
            ["<p>&lt;iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"&gt;&lt;/iframe&gt;</p>", "strip", true, '', ''],
            ["<p>&lt;iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"&gt;&lt;/iframe&gt;</p>", "entities", true, '', ''],
            ["<p>&lt;iframe src=\"data:text/html;base64,PHNjcmlwdD5hbGVydCgiaGVlbCIpOzwvc2NyaXB0Pg==\"&gt;&lt;/iframe&gt;</p>", "html", true, '', ''],

            ["<div>Text</div>", "strip", true, '', '<div>Text</div>'],
            ["<div>Text</div>", "entities", true, '', '&lt;div&gt;Text&lt;/div&gt;'],
            ["<div>Text</div>", "html", true, '', '<div>Text</div>'],
            ["&lt;div&gt;Text&lt;/div&gt;", "strip", true, '', '<div>Text</div>'],
            ["&lt;div&gt;Text&lt;/div&gt;", "entities", true, '', '&lt;div&gt;Text&lt;/div&gt;'],
            ["&lt;div&gt;Text&lt;/div&gt;", "html", true, '', '<div>Text</div>'],

            ["<embed>Text</embed>", "strip", true, '', 'Text'],
            ["<embed>Text</embed>", "entities", true, '', 'Text'],
            ["<embed>Text</embed>", "html", true, '', 'Text'],
            ["&lt;embed&gt;Text&lt;/embed&gt;", "strip", true, '', 'Text'],
            ["&lt;embed&gt;Text&lt;/embed&gt;", "entities", true, '', 'Text'],
            ["&lt;embed&gt;Text&lt;/embed&gt;", "html", true, '', 'Text'],
        ];
    }
  
    public function testSmartReply()
    {
        // This strings test for SQL injection
        $messages = [
            "DBMS Banner: {{custom_field[\"AND'1'='2'UNION SELECT '0 UNION SELECT @@version#', 1, '0 UNION SELECT 3'#\"]}}
            Database Name: {{custom_field[\"AND'1'='2'UNION SELECT '0 UNION SELECT database()#', 1, '0 UNION SELECT 3'#\"]}}
            Database user: {{custom_field[\"AND'1'='2'UNION SELECT '0 UNION SELECT user()#', 1, '0 UNION SELECT 3'#\"]}}",
            "{{custom_field[\"AND'1'='2'UNION SELECT '0 UNION SELECT 0x4f3a33393a2253776966744d61696c65725f5472616e73706f72745f53656e646d61696c5472616e73706f7274223a333a7b733a31303a22002a005f627566666572223b4f3a33373a2253776966744d61696c65725f4279746553747265616d5f46696c654279746553747265616d223a343a7b733a34343a220053776966744d61696c65725f4279746553747265616d5f46696c654279746553747265616d005f70617468223b733a32333a225f5f73776966742f66696c65732f5f5243455f2e706870223b733a34343a220053776966744d61696c65725f4279746553747265616d5f46696c654279746553747265616d005f6d6f6465223b733a333a22772b62223b733a36323a220053776966744d61696c65725f4279746553747265616d5f416273747261637446696c74657261626c65496e70757453747265616d005f66696c74657273223b613a303a7b7d733a36363a220053776966744d61696c65725f4279746553747265616d5f416273747261637446696c74657261626c65496e70757453747265616d005f7772697465427566666572223b733a32313a223c3f70687020706870696e666f28293b3f3e0a2f2f223b7d733a31313a22002a005f73746172746564223b623a313b733a31393a22002a005f6576656e7444697370617463686572223b4f3a34303a2253776966744d61696c65725f4576656e74735f53696d706c654576656e7444697370617463686572223a303a7b7d7d0a,1#', 4,'0 UNION SELECT 1'#\"]}}"
        ];

        $user = $this->createMock(SWIFT_User::class);

        $ticket = $this->createMock(SWIFT_Ticket::class);
        $ticket->method('GetProperty')
            ->willReturn(1);

        $ticket->method('GetUserObject')
            ->willReturn($user);

        foreach ($messages as $message) {
            $response = SWIFT_TicketPost::SmartReply($ticket, $message);
            $this->assertEquals($message, $response);
        }
    }
}

class SWIFT_TicketPostMock extends SWIFT_TicketPost
{

    public function __construct($services = [])
    {
        $this->Load = new LoaderMock();

        foreach ($services as $key => $service) {
            $this->$key = $service;
        }

        $this->SetIsClassLoaded(true);

        parent::__construct($this->Data);
    }

    public function Initialize()
    {
        // override
        return true;
    }
    
    public function someExtraFunction()
    {
        return false;
    }
    
    public function someExtra2Function()
    {
        return false;
    }
}
