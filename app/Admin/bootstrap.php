<?php

// PackageManager::load('admin-default')
//    ->css('extend', resources_url('css/extend.css'));

use SleepingOwl\Admin\Model\ModelConfiguration;

AdminSection::registerModel(\App\User::class, function (ModelConfiguration $model) {
    $model->setTitle('Users');

    $model->onDisplay(function () {
        $display = AdminDisplay::datatables()->setColumns([
            AdminColumn::text('id', 'ID'),
            AdminColumn::link('name')->setLabel('Name'),
            AdminColumn::image('photo', 'Photo'),
            AdminColumn::email('email', 'Email'),
            AdminColumnEditable::checkbox('admin', 'Yes', 'No')->setLabel('Admin'),
        ]);
        $display->paginate(20);

        return $display;
    });

    $model->onCreateAndEdit(function () {
        $form = AdminForm::panel()->addBody([
            AdminFormElement::text('name', 'Name')->required(),
            AdminFormElement::image('photo', 'Photo'),
            AdminFormElement::text('email', 'email')->unique(),
            AdminFormElement::password('password', 'password')->hashWithBcrypt(),
            AdminFormElement::checkbox('admin', 'Admin'),
        ]);
        return $form;
    });

    $model->setIcon('fa fa-user');
    $model->addToNavigation();
});

AdminSection::registerModel(\App\Event::class, function (ModelConfiguration $model) {
    $model->setTitle('Events');

    $model->updating(function (ModelConfiguration $model, \App\Event $event) {
        $event->geocode();
    });

    $model->onDisplay(function () {
        $display = AdminDisplay::datatables()->setColumns([
            AdminColumn::text('id', 'ID'),
            AdminColumn::link('name')->setLabel('Name'),
            AdminColumn::datetime('time', 'Time')->setFormat('m/d/Y H:i'),
            AdminColumn::text('address', 'Address'),
            AdminColumn::lists('drivers.name', 'Drivers'),
            AdminColumn::text('note', 'Note')
        ]);
        $display->paginate(20);

        return $display;
    });

    $model->onCreateAndEdit(function () {
        $form = AdminForm::panel()->addBody([
            AdminFormElement::text('name', 'Name')->required(),
            AdminFormElement::text('address', 'Address')->required(),
            AdminFormElement::view('admin.event.map'),
            AdminFormElement::datetime('time', 'Time')->required(),
            AdminFormElement::textarea('note', 'Note'),
        ]);
        return $form;
    });

    $model->setIcon('fa fa-calendar');
    $model->addToNavigation();
});
