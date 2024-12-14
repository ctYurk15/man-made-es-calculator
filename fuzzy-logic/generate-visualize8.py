import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json
import os

# Універсум значень для дати останньої перевірки
last_check = np.arange(0, 13, 1)

# Визначення нечітких множин для дати останньої перевірки
low = fuzz.trapmf(last_check, [0, 0, 2, 4])        # Нещодавно (хороший стан)
moderate = fuzz.trimf(last_check, [3, 6, 9])      # Середній стан
high = fuzz.trapmf(last_check, [8, 10, 12, 12])   # Застарілий (поганий стан)

# Функція для визначення категорії на основі нечіткої логіки
def get_check_category(value):
    low_value = fuzz.interp_membership(last_check, low, value)
    moderate_value = fuzz.interp_membership(last_check, moderate, value)
    high_value = fuzz.interp_membership(last_check, high, value)

    # Ступені належності
    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    # Визначення категорії з найвищим ступенем належності
    return max(memberships, key=memberships.get)

# Генерація даних для всіх значень
data = [{"value": value, "category": get_check_category(value)} for value in range(13)]

# Створення директорії, якщо її немає
output_dir = "categories-data"
os.makedirs(output_dir, exist_ok=True)

# Запис результатів у JSON-файл
output_path = os.path.join(output_dir, "last_check.json")
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

# Візуалізація нечітких множин
plt.figure(figsize=(10, 6))
plt.plot(last_check, low, label="Low (Нещодавно)", color="blue")
plt.plot(last_check, moderate, label="Moderate (Середній стан)", color="green")
plt.plot(last_check, high, label="High (Застарілий стан)", color="red")

# Додавання підписів і легенди
plt.title("Нечіткі множини для дати останньої перевірки", fontsize=14)
plt.xlabel("Місяці з моменту останньої перевірки", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
