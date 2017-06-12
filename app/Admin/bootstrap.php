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
        //return $event->lat !== null;
    });
    $model->creating(function (ModelConfiguration $model, \App\Event $event) {
        $event->geocode();
        //return $event->lat !== null;
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
        $display->addScript('datatables.responsive', asset('js/dataTables.responsive.min.js'), ['admin-default']);
        return $display;
    });

    $model->onCreateAndEdit(function () {
        $form = AdminForm::panel()->addBody([
            AdminFormElement::text('name', 'Name')->required(),
            AdminFormElement::datetime('time', 'Time')->required(),
            AdminFormElement::wysiwyg('congratulations', 'Congratulations', 'ckeditor')->required(),
            AdminFormElement::textarea('note', 'Note'),
            AdminFormElement::text('address', 'Address'),
            AdminFormElement::view('admin.event.map'),

            AdminFormElement::text('main_bg_color', 'Main background color'),
            AdminFormElement::image('main_bg_image', 'Main background image'),
            AdminFormElement::text('area_bg_color', 'Text area background color'),
            AdminFormElement::image('area_bg_image', 'Text area background image'),
            AdminFormElement::text('area_text_color', 'Text color'),
            AdminFormElement::text('area_timer_color', 'Countdown color'),
            AdminFormElement::text('area_arrived_color', '"Arrived" color'),
        ]);
        $form->addScript('jquery-js', asset('js/jquery.min.js'));
        $form->addScript('colorpicker-js', asset('js/jquery.minicolors.min.js'));
        $form->addStyle('colorpicker-css', asset('js/jquery.minicolors.css'));
        $form->addScript('setpicker-js', asset('js/setpicker.js'));
        return $form;
    });

    $model->setIcon('fa fa-calendar');
    $model->addToNavigation();
});
