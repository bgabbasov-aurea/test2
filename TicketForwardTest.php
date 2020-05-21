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

namespace Tickets\Models\Ticket;

use Knowledgebase\Admin\LoaderMock;
use SWIFT_Exception;

/**
* Class TicketPostTest
* @group tickets
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
}
