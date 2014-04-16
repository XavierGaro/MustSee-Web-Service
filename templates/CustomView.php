<?php
// El template hereda de \Slim\View, devuelve una string con la vista formateada. A diferencia del string se formatean como clases.
class CustomView extends \Slim\View {
    public function render($template) {
        $color = $this->data['color'];
        $size = $this->data['size'];
        return "Esta es la CustomView color: $color size: $size ";
    }
}