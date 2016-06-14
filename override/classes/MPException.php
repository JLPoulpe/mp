<?php
/**
 * @author Julien
 */
class MPException extends Exception {
    static public $codeCpIsMissing = 1;
    static public $stringCpIsMissing = 'codeCpIsMissing';
    static public $codeCpIsWrong = 2;
    static public $stringCpIsWront = 'codeCpIsWrong';
    static public $codeNoMarket = 3;
    static public $codeNoSupplier = 4;
    static public $codeNoCategory = 5;
}
