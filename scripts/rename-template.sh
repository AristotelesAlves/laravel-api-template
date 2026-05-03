#!/usr/bin/env sh
set -eu

PROJECT="${PROJECT:-}"
VENDOR="${VENDOR:-app}"

if [ -z "$PROJECT" ]; then
    echo "Missing PROJECT. Usage: make rename PROJECT=my-api"
    exit 1
fi

case "$PROJECT" in
    *[!a-zA-Z0-9_-]*)
        echo "Invalid PROJECT '$PROJECT'. Use only letters, numbers, hyphens, and underscores."
        exit 1
        ;;
esac

SLUG="$(printf '%s' "$PROJECT" | tr '[:upper:]' '[:lower:]' | tr '_' '-')"
ENV_NAME="$(printf '%s' "$SLUG" | tr '-' '_')"
TITLE="$(printf '%s' "$SLUG" | tr '-' ' ' | awk '{
    for (i = 1; i <= NF; i++) {
        $i = toupper(substr($i, 1, 1)) substr($i, 2)
    }
    print
}')"
APP_NAME="$TITLE"
COMPOSER_NAME="$VENDOR/$SLUG"
DESCRIPTION="Laravel API for $TITLE."

replace_or_append_env() {
    file="$1"
    key="$2"
    value="$3"

    if [ ! -f "$file" ]; then
        return
    fi

    escaped_value="$(printf '%s' "$value" | sed 's/[\/&]/\\&/g')"

    if grep -q "^${key}=" "$file"; then
        sed -i "s/^${key}=.*/${key}=${escaped_value}/" "$file"
    else
        printf '\n%s=%s\n' "$key" "$value" >> "$file"
    fi
}

replace_in_file() {
    file="$1"
    pattern="$2"
    replacement="$3"

    if [ ! -f "$file" ]; then
        return
    fi

    escaped_pattern="$(printf '%s' "$pattern" | sed 's/[\/&]/\\&/g')"
    escaped_replacement="$(printf '%s' "$replacement" | sed 's/[\/&]/\\&/g')"
    sed -i "s/${escaped_pattern}/${escaped_replacement}/g" "$file"
}

for env_file in .env .env.example; do
    replace_or_append_env "$env_file" "APP_NAME" "\"$APP_NAME\""
    replace_or_append_env "$env_file" "COMPOSE_PROJECT_NAME" "$SLUG"
    replace_or_append_env "$env_file" "DB_DATABASE" "$ENV_NAME"
    replace_or_append_env "$env_file" "DB_USERNAME" "$ENV_NAME"
done

if [ -f composer.json ]; then
    escaped_composer_name="$(printf '%s' "$COMPOSER_NAME" | sed 's/[|&]/\\&/g')"
    escaped_description="$(printf '%s' "$DESCRIPTION" | sed 's/[|&]/\\&/g')"
    sed -i "s|\"name\": \"[^\"]*\"|\"name\": \"$escaped_composer_name\"|" composer.json
    sed -i "s|\"description\": \"[^\"]*\"|\"description\": \"$escaped_description\"|" composer.json
fi

replace_in_file README.md "# Laravel API Template" "# $TITLE"
replace_in_file README.md "Laravel API Template logo" "$TITLE logo"
replace_in_file README.md "Laravel API Template" "$TITLE"
replace_in_file README.md "laravel_template" "$ENV_NAME"

echo "Template renamed successfully."
echo "Project title: $TITLE"
echo "Slug: $SLUG"
echo "Database: $ENV_NAME"
echo "Composer package: $COMPOSER_NAME"
echo "Next step: run composer update --lock or make composer CMD=\"update --lock\" after dependencies are available."
