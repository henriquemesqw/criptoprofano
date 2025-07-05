<?php
// Definição da chave secreta usada na criptografia
// A chave precisa ter 32 bytes para o AES-256 funcionar corretamente
define('CHAVE_SECRETA', 'minha_chave_super_secreta_1234567'); // (32 caracteres)

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = $_POST['mensagem']; // Mensagem recebida do formulário
    $acao = $_POST['acao'];         // Ação escolhida: criptografar ou descriptografar

    // Realiza a ação escolhida e exibe o resultado
    if ($acao == "criptografar") {
        $mensagem_criptografada = criptografar($mensagem); // Chama a função para criptografar
        echo "<p><strong>Mensagem Criptografada:</strong> " . htmlspecialchars($mensagem_criptografada) . "</p>"; // Exibe resultado
    } elseif ($acao == "descriptografar") {
        $mensagem_descriptografada = descriptografar($mensagem); // Chama a função para descriptografar
        echo "<p><strong>Mensagem Descriptografada:</strong> " . htmlspecialchars($mensagem_descriptografada) . "</p>"; // Exibe resultado
    }
}

// Função para criptografar uma mensagem com AES-256-CBC e IV aleatório
function criptografar($mensagem) {
    $iv = openssl_random_pseudo_bytes(16); // Gera IV aleatório de 16 bytes (necessário para AES-CBC)

    // Criptografa a mensagem usando AES-256-CBC
    $criptografada = openssl_encrypt(
        $mensagem,             // Texto original
        'aes-256-cbc',         // Algoritmo de criptografia
        CHAVE_SECRETA,         // Chave de criptografia
        OPENSSL_RAW_DATA,      // Retorna dados binários
        $iv                    // Vetor de inicialização
    );

    // Concatena o IV com o texto criptografado e codifica em base64 para facilitar o envio
    return base64_encode($iv . $criptografada);
}

// Função para descriptografar a mensagem recebida
function descriptografar($mensagem) {
    // Decodifica a string base64
    $dados = base64_decode($mensagem, true);

    // Verifica se a string é válida e tem pelo menos 17 bytes (16 do IV + algo criptografado)
    if ($dados === false || strlen($dados) <17) {
        return "Erro: Mensagem inválida.";
    }

    // Extrai os 16 primeiros bytes como IV e o restante como a mensagem criptografada
    $iv = substr($dados, 0, 16);
    $conteudo = substr($dados, 16);

    // Descriptografa usando o mesmo algoritmo, chave e IV
    $descriptografada = openssl_decrypt(
        $conteudo,             // Texto criptografado
        'aes-256-cbc',         // Algoritmo
        CHAVE_SECRETA,         // Chave
        OPENSSL_RAW_DATA,      // Dados brutos
        $iv                    // IV usado na criptografia
    );

    // Verifica se a descriptografia foi bem-sucedida
    if ($descriptografada === false) {
        return "Erro: Não foi possível descriptografar.";
    }

    return $descriptografada; // Retorna o texto descriptografado
}
?>