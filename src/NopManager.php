<?php


namespace IdeaToCode\LaravelNovaTallPayments;


class NopManager
{
    public function initSubscription()
    {
        return true;
    }
    public function syncSubscription()
    {
        return "Nothing happened";
    }
    public function getManagementRoute()
    {
        return "invoice.show";
    }
}
