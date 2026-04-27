#!/bin/bash

# 1. جمع كل البلجنز وملفات ServiceProvider
PLUGINS_DIR="plugins/webkul"
declare -A DEPS
declare -A NAMES

# 2. استخراج اسم البلجن والاعتماديات من كل ServiceProvider
for sp in $(find "$PLUGINS_DIR" -name '*ServiceProvider.php'); do
    name=$(grep -m1 "name\s*=" "$sp" | sed -E "s/.*name\s*=\s*'([^']+)'.*/\1/")
    deps=$(grep -A2 "hasDependencies" "$sp" | grep -oE "'[a-zA-Z0-9_-]+'" | tr -d "'")
    NAMES["$name"]="$sp"
    DEPS["$name"]="$deps"
done

# 3. Topological sort (ترتيب حسب الاعتماديات)
ordered=()
visited=()

visit() {
    local plugin=$1
    [[ " ${visited[@]} " =~ " $plugin " ]] && return
    visited+=("$plugin")
    for dep in ${DEPS[$plugin]}; do
        [[ -n "$dep" ]] && visit "$dep"
    done
    ordered+=("$plugin")
}

for plugin in "${!NAMES[@]}"; do
    visit "$plugin"
done

# 4. طباعة أوامر التثبيت مرتبة
echo "==== أوامر تثبيت البلجنز مرتبة حسب الاعتماديات ===="
for plugin in "${ordered[@]}"; do
    [[ -n "$plugin" ]] && echo "php artisan plugin:install $plugin"
done