<?php

namespace Slice\Form\Renderer;


use Slice\Form\Field\FormFieldInterface;
use Slice\Form\FormUtils;

class BulmaFormRenderer implements FormRendererInterface
{

    public function renderField(FormFieldInterface $input, $formName = '')
    {
        $objectType = $this->getInputType($input);

        ob_start();
        require __DIR__ . '/../Resources/templates/bulma/' . $objectType . '.php';

        return ob_get_clean();
    }

    public function renderForm(\Slice\Form\Form $form)
    {
        $content = null;

        foreach ($form->getFields() as $field) {
            $content .= $this->renderField($field, $form->getName());
        }

        ob_start();
        require __DIR__ . '/../Resources/templates/default/form.php';

        return ob_get_clean();
    }

    public function setCustomFieldTemplate($field, $templatePath)
    {
        // TODO: Implement setCustomFieldTemplate() method.
    }

    private function getInputType(FormFieldInterface $input)
    {
        $explodedClassName = explode('\\', \get_class($input));
        return FormUtils::decamelize(end($explodedClassName));
    }
}