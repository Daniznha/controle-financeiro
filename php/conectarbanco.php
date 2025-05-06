<?php
class conectarbanco {
    private $nome_servidor = "127.0.0.1";
    private $nome_usuario = "adminCG";
    private $senha = "CG123";
    private $banco = "controleGastos";
    private $porta = "3306";
    private $conecta;
    
    
    function __construct() {
        
    }

    public function conectar() {
        $this->conecta = new mysqli($this->nome_servidor, $this->nome_usuario, $this->senha, $this->banco, $this->porta);
        // Verificar Conexão
        if ($this->conecta->connect_error) {
            die("Conexão falhou: " . $this->conecta->connect_error."<br>");
        }
        return $this->conecta;
    }
    
    public function desconectar() {
        $this->conecta->close();
    }
    
    public function getConecta() {
        return $this->conecta;
    }
    
    

}