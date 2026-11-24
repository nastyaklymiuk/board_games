<?php

namespace App\Controller\Admin;

use App\Entity\BoardGame;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(BoardGameCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Настільні ігри: адмінка');
    }

        public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(CategoryCrudController::class, 'Категорії', 'fa fa-list');
        yield MenuItem::linkTo(BoardGameCrudController::class, 'Настільні ігри', 'fa fa-dice');
        yield MenuItem::linkToRoute('На сайт', 'fa fa-arrow-left', 'app_home');
    }
}