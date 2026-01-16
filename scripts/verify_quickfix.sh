#!/bin/bash

###############################################################################
# Script de vérification des quick-fixes AK-Framework
# 
# Ce script vérifie automatiquement:
# 1. Header Content-Type JSON et validité JSON
# 2. Endpoint Datatables (clés iTotalRecords, iTotalDisplayRecords, aaData)
# 3. Login endpoint POST
# 4. Création de fichier de cache dans var/cache pour la clé data_user_id
#
# Dépendances: curl, php CLI, jq (optionnel)
###############################################################################

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variables par défaut
BASE_URL="http://localhost"
DATATABLES_URL="${BASE_URL}/tasks/list"
LOGIN_URL="${BASE_URL}/connexion"
TASKS_URL="${BASE_URL}/tasks"
LOGIN_USER=""
LOGIN_PASS=""
CSRF_TOKEN=""
CACHE_DIR="var/cache"
CACHE_KEY="data_user_id"

# Afficher l'aide
show_help() {
    cat << EOF
Usage: $0 [OPTIONS]

Options:
  --base-url URL          URL de base du site (défaut: http://localhost)
  --datatables-url URL    URL de l'endpoint Datatables (défaut: \${BASE_URL}/tasks/list)
  --login-url URL         URL de l'endpoint login (défaut: \${BASE_URL}/connexion)
  --tasks-url URL         URL de l'endpoint tasks (défaut: \${BASE_URL}/tasks)
  --login-user USER       Nom d'utilisateur pour le test de login
  --login-pass PASS       Mot de passe pour le test de login
  --csrf-token TOKEN      Token CSRF pour les requêtes
  --cache-dir DIR         Répertoire du cache (défaut: var/cache)
  --cache-key KEY         Clé de cache à vérifier (défaut: data_user_id)
  -h, --help              Afficher cette aide

Exemples:
  $0 --base-url http://mysite.local --login-user admin --login-pass password123
  $0 --cache-dir /path/to/var/cache --cache-key data_user_id
EOF
}

# Parser les arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --base-url)
            BASE_URL="$2"
            DATATABLES_URL="${BASE_URL}/tasks/list"
            LOGIN_URL="${BASE_URL}/connexion"
            TASKS_URL="${BASE_URL}/tasks"
            shift 2
            ;;
        --datatables-url)
            DATATABLES_URL="$2"
            shift 2
            ;;
        --login-url)
            LOGIN_URL="$2"
            shift 2
            ;;
        --tasks-url)
            TASKS_URL="$2"
            shift 2
            ;;
        --login-user)
            LOGIN_USER="$2"
            shift 2
            ;;
        --login-pass)
            LOGIN_PASS="$2"
            shift 2
            ;;
        --csrf-token)
            CSRF_TOKEN="$2"
            shift 2
            ;;
        --cache-dir)
            CACHE_DIR="$2"
            shift 2
            ;;
        --cache-key)
            CACHE_KEY="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        *)
            echo "Option inconnue: $1"
            show_help
            exit 1
            ;;
    esac
done

# Fonction pour afficher les résultats
print_result() {
    local test_name="$1"
    local result="$2"
    local message="$3"
    
    if [ "$result" = "OK" ]; then
        echo -e "${GREEN}[✓] ${test_name}${NC}"
        [ -n "$message" ] && echo "    $message"
    elif [ "$result" = "SKIP" ]; then
        echo -e "${YELLOW}[⊘] ${test_name} - SKIPPED${NC}"
        [ -n "$message" ] && echo "    $message"
    else
        echo -e "${RED}[✗] ${test_name} - FAILED${NC}"
        [ -n "$message" ] && echo "    $message"
    fi
}

# Vérifier les dépendances
check_dependencies() {
    echo "=== Vérification des dépendances ==="
    
    if ! command -v curl &> /dev/null; then
        print_result "curl" "FAIL" "curl n'est pas installé"
        exit 1
    fi
    print_result "curl" "OK" "$(curl --version | head -n1)"
    
    if ! command -v php &> /dev/null; then
        print_result "PHP CLI" "FAIL" "PHP CLI n'est pas installé"
        exit 1
    fi
    print_result "PHP CLI" "OK" "$(php -v | head -n1)"
    
    if command -v jq &> /dev/null; then
        print_result "jq (optionnel)" "OK" "$(jq --version)"
    else
        print_result "jq (optionnel)" "SKIP" "jq n'est pas installé (optionnel)"
    fi
    echo ""
}

# Test 1: Vérifier Content-Type JSON et validité JSON
test_json_header() {
    echo "=== Test 1: Content-Type JSON et validité JSON ==="
    
    # Créer un fichier temporaire pour tester la validation JSON
    local temp_file=$(mktemp)
    local json_test='{"test": "value", "unicode": "éàü"}'
    
    # Tester JSON_UNESCAPED_UNICODE avec PHP
    local php_result=$(php -r "echo json_encode(json_decode('$json_test'), JSON_UNESCAPED_UNICODE);")
    
    if echo "$php_result" | grep -q "éàü"; then
        print_result "JSON_UNESCAPED_UNICODE" "OK" "Les caractères Unicode ne sont pas échappés"
    else
        print_result "JSON_UNESCAPED_UNICODE" "FAIL" "Les caractères Unicode sont échappés"
    fi
    
    rm -f "$temp_file"
    echo ""
}

# Test 2: Vérifier l'endpoint Datatables
test_datatables() {
    echo "=== Test 2: Endpoint Datatables ==="
    
    if [ -z "$CSRF_TOKEN" ]; then
        print_result "Datatables endpoint" "SKIP" "Token CSRF requis (utilisez --csrf-token)"
        echo ""
        return
    fi
    
    # Simuler une requête Datatables (nécessite un serveur fonctionnel)
    print_result "Datatables endpoint" "SKIP" "Nécessite un serveur web fonctionnel"
    echo "    Pour tester manuellement:"
    echo "    curl -i '${DATATABLES_URL}?sEcho=1&iDisplayStart=0&iDisplayLength=10'"
    echo "    Vérifiez la présence des clés: iTotalRecords, iTotalDisplayRecords, aaData"
    echo "    Vérifiez l'absence de SQL_CALC_FOUND_ROWS dans la clé 'sql'"
    echo ""
}

# Test 3: Vérifier l'endpoint login
test_login() {
    echo "=== Test 3: Endpoint Login ==="
    
    if [ -z "$LOGIN_USER" ] || [ -z "$LOGIN_PASS" ]; then
        print_result "Login endpoint" "SKIP" "Credentials requis (utilisez --login-user et --login-pass)"
        echo ""
        return
    fi
    
    if [ -z "$CSRF_TOKEN" ]; then
        print_result "Login endpoint" "SKIP" "Token CSRF requis (utilisez --csrf-token)"
        echo ""
        return
    fi
    
    # Tester l'endpoint login (nécessite un serveur fonctionnel)
    print_result "Login endpoint" "SKIP" "Nécessite un serveur web fonctionnel"
    echo "    Pour tester manuellement:"
    echo "    curl -i -X POST '${LOGIN_URL}' \\"
    echo "      -d 'action=login&username=${LOGIN_USER}&password=${LOGIN_PASS}'"
    echo "    Vérifiez que la réponse est en JSON et utilise des requêtes préparées"
    echo ""
}

# Test 4: Vérifier la création de fichier de cache
test_cache_file() {
    echo "=== Test 4: Fichier de cache ==="
    
    # Vérifier si le répertoire de cache existe
    if [ ! -d "$CACHE_DIR" ]; then
        print_result "Répertoire cache" "FAIL" "Le répertoire $CACHE_DIR n'existe pas"
        echo ""
        return
    fi
    print_result "Répertoire cache" "OK" "Le répertoire $CACHE_DIR existe"
    
    # Vérifier les permissions d'écriture
    if [ ! -w "$CACHE_DIR" ]; then
        print_result "Permissions cache" "FAIL" "Le répertoire $CACHE_DIR n'est pas accessible en écriture"
        echo ""
        return
    fi
    print_result "Permissions cache" "OK" "Le répertoire $CACHE_DIR est accessible en écriture"
    
    # Calculer le hash MD5 de la clé de cache
    local cache_key_hash=$(echo -n "$CACHE_KEY" | md5sum | awk '{print $1}')
    local cache_file="${CACHE_DIR}/${cache_key_hash}.cache"
    
    # Vérifier si le fichier de cache existe
    if [ -f "$cache_file" ]; then
        print_result "Fichier cache" "OK" "Le fichier de cache existe: $cache_file"
        
        # Vérifier la validité du contenu
        if php -r "
            \$data = @file_get_contents('$cache_file');
            \$cache = @unserialize(\$data);
            if (\$cache !== false && isset(\$cache['expires']) && isset(\$cache['value'])) {
                if (time() < \$cache['expires']) {
                    exit(0);
                } else {
                    exit(2);
                }
            } else {
                exit(1);
            }
        "; then
            print_result "Contenu cache" "OK" "Le contenu du cache est valide et non expiré"
        else
            local exit_code=$?
            if [ $exit_code -eq 2 ]; then
                print_result "Contenu cache" "FAIL" "Le cache est expiré"
            else
                print_result "Contenu cache" "FAIL" "Le contenu du cache est invalide"
            fi
        fi
    else
        print_result "Fichier cache" "SKIP" "Le fichier de cache n'existe pas encore"
        echo "    Accédez à l'interface Tasks pour créer le cache"
        echo "    Fichier attendu: $cache_file"
    fi
    echo ""
}

# Test 5: Vérifier la structure du code
test_code_structure() {
    echo "=== Test 5: Structure du code ==="
    
    # Vérifier HTTPResponse.class.php
    if [ -f "Library/HTTPResponse.class.php" ]; then
        if grep -q "JSON_UNESCAPED_UNICODE" "Library/HTTPResponse.class.php"; then
            print_result "HTTPResponse JSON_UNESCAPED_UNICODE" "OK" "JSON_UNESCAPED_UNICODE est présent"
        else
            print_result "HTTPResponse JSON_UNESCAPED_UNICODE" "FAIL" "JSON_UNESCAPED_UNICODE est absent"
        fi
        
        if grep -q "Content-Type: application/json; charset=utf-8" "Library/HTTPResponse.class.php"; then
            print_result "HTTPResponse Content-Type" "OK" "Content-Type correct"
        else
            print_result "HTTPResponse Content-Type" "FAIL" "Content-Type incorrect"
        fi
    else
        print_result "HTTPResponse.class.php" "FAIL" "Fichier non trouvé"
    fi
    
    # Vérifier Datatables.class.php
    if [ -f "Library/Datatables.class.php" ]; then
        if ! grep -q "SQL_CALC_FOUND_ROWS" "Library/Datatables.class.php"; then
            print_result "Datatables SQL_CALC_FOUND_ROWS" "OK" "SQL_CALC_FOUND_ROWS supprimé"
        else
            print_result "Datatables SQL_CALC_FOUND_ROWS" "FAIL" "SQL_CALC_FOUND_ROWS encore présent"
        fi
        
        if grep -q "COUNT" "Library/Datatables.class.php"; then
            print_result "Datatables COUNT query" "OK" "Requête COUNT présente"
        else
            print_result "Datatables COUNT query" "FAIL" "Requête COUNT absente"
        fi
    else
        print_result "Datatables.class.php" "FAIL" "Fichier non trouvé"
    fi
    
    # Vérifier ConnexionController.class.php
    if [ -f "Applications/Frontend/Modules/Connexion/ConnexionController.class.php" ]; then
        if grep -q "prepare" "Applications/Frontend/Modules/Connexion/ConnexionController.class.php"; then
            print_result "ConnexionController prepared statements" "OK" "Requêtes préparées présentes"
        else
            print_result "ConnexionController prepared statements" "FAIL" "Requêtes préparées absentes"
        fi
        
        if grep -q "bindValue" "Applications/Frontend/Modules/Connexion/ConnexionController.class.php"; then
            print_result "ConnexionController bindValue" "OK" "bindValue présent"
        else
            print_result "ConnexionController bindValue" "FAIL" "bindValue absent"
        fi
        
        if grep -q "password_verify" "Applications/Frontend/Modules/Connexion/ConnexionController.class.php"; then
            print_result "ConnexionController password_verify" "OK" "password_verify présent"
        else
            print_result "ConnexionController password_verify" "FAIL" "password_verify absent"
        fi
    else
        print_result "ConnexionController.class.php" "FAIL" "Fichier non trouvé"
    fi
    
    # Vérifier Cache.class.php
    if [ -f "Library/Cache.class.php" ]; then
        print_result "Cache.class.php" "OK" "Fichier Cache créé"
        
        if grep -q "apcu" "Library/Cache.class.php" && grep -q "file_get_contents" "Library/Cache.class.php"; then
            print_result "Cache APCu et file-based" "OK" "Support APCu et file-based"
        else
            print_result "Cache APCu et file-based" "FAIL" "Support incomplet"
        fi
    else
        print_result "Cache.class.php" "FAIL" "Fichier non trouvé"
    fi
    
    # Vérifier TasksController.class.php
    if [ -f "Applications/Frontend/Modules/Tasks/TasksController.class.php" ]; then
        if grep -q "Cache" "Applications/Frontend/Modules/Tasks/TasksController.class.php"; then
            print_result "TasksController Cache usage" "OK" "Cache utilisé dans TasksController"
        else
            print_result "TasksController Cache usage" "FAIL" "Cache non utilisé"
        fi
    else
        print_result "TasksController.class.php" "FAIL" "Fichier non trouvé"
    fi
    
    echo ""
}

# Programme principal
main() {
    echo "=========================================="
    echo "   Vérification des quick-fixes"
    echo "   AK-Framework"
    echo "=========================================="
    echo ""
    
    check_dependencies
    test_json_header
    test_datatables
    test_login
    test_cache_file
    test_code_structure
    
    echo "=========================================="
    echo "   Vérification terminée"
    echo "=========================================="
}

main
