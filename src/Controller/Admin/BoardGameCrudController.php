<?php

namespace App\Controller\Admin;

use App\Entity\BoardGame;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BoardGameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BoardGame::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Назва');
        yield AssociationField::new('category', 'Категорія');
        yield TextField::new('price', 'Ціна, грн');
        yield IntegerField::new('minPlayers', 'Мін. гравців');
        yield IntegerField::new('maxPlayers', 'Макс. гравців');
        yield IntegerField::new('playTimeMinutes', 'Хвилин');
        yield IntegerField::new('minAge', 'Вік');
        yield BooleanField::new('inStock', 'В наявності');
        yield TextEditorField::new('description', 'Опис')->hideOnIndex();
    }
}