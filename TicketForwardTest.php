<?php
class SWIFT_TicketPostMock extends SWIFT_TicketPost
{
    public function Initialize()
    {
        // override
        return true;
    }
    
    public function someExtraFunction()
    {
        return false;
    }
}
?>
