<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jadob\Form\Renderer;

use Jadob\Form\Field\FormFieldInterface;
/**
 * @author pizzaminded <miki@appvende.net>
 */
interface FormRendererInterface {
    //put your code here
    public function renderField(FormFieldInterface $input);
    
    public function renderForm(\Jadob\Form\Form $form);

    public function setCustomFieldTemplate($field, $templatePath);
}