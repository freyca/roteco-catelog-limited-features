<?php

declare(strict_types=1);

namespace App\Factories\BreadCrumbs;

class StandardPageBreadCrumbs implements BreadCrumbsFactory
{
    protected array $default_bread_crumb = [];

    protected array $bread_crumbs = [];

    public function __construct(array $bread_crumbs)
    {
        $this->setDefaultBreadCrumb();

        $this->bread_crumbs = array_merge($this->default_bread_crumb, $bread_crumbs);
    }

    public function getBreadCrumbs(): array
    {
        return $this->bread_crumbs;
    }

    protected function setDefaultBreadCrumb(): void
    {
        $this->default_bread_crumb = ['heroicon-m-home' => route('home')];
    }
}
