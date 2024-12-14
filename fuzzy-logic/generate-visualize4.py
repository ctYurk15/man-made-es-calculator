import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json

# Універсум значень для рівня зношеності
value = np.arange(0, 101, 1)

# Визначення нечітких множин для рівня зношеності
low = fuzz.trapmf(value, [0, 0, 25, 45])         # Низький рівень зношеності
moderate = fuzz.trimf(value, [30, 50, 70])       # Помірний рівень зношеності
high = fuzz.trapmf(value, [60, 75, 100, 100])    # Високий рівень зношеності

# Функція для визначення категорії на основі нечіткої логіки
def get_wear_category(value_level):
    low_value = fuzz.interp_membership(value, low, value_level)
    moderate_value = fuzz.interp_membership(value, moderate, value_level)
    high_value = fuzz.interp_membership(value, high, value_level)

    # Ступені належності
    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    # Визначення категорії з найвищим ступенем належності
    return max(memberships, key=memberships.get)

# Генерація даних для всіх значень
data = [{"value": value_level, "category": get_wear_category(value_level)} for value_level in range(101)]

# Запис результатів у JSON-файл
with open("categories-data/equipment_wear.json", "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

# Візуалізація нечітких множин
plt.figure(figsize=(10, 6))
plt.plot(value, low, label="Low (Низький рівень зношеності)", color="blue")
plt.plot(value, moderate, label="Moderate (Помірний рівень зношеності)", color="green")
plt.plot(value, high, label="High (Високий рівень зношеності)", color="orange")

# Додавання підписів і легенди
plt.title("Нечіткі множини для рівня зношеності", fontsize=14)
plt.xlabel("Value (%)", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
