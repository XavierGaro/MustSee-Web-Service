<?php
namespace Serializer;

/**
 * Interface Serializer
 * Cada una de les classes que implementen aquesta interfície permet seriar les dades que es
 * passen per argument en un tipus diferent de format. Les dades poden ser objectes,
 * arrays o elements primitius.
 *
 * En el cas d'objectes obté totes les propietats públiques i les privades que tinguin un getter
 * dels objectes. El getter ha de seguir el format 'getNomPropietat' exactament per ser reconegut
 * i ha de ser públic.
 *
 * @package Serializer
 */
interface Serializer {

    /**
     * Retorna les dades seriades en el format adequat per la classe concreta.
     *
     * @param mixed|mixed[] $data dades per seriar
     * @return string dades seriades
     */
    public function getSerialized($data);
} 