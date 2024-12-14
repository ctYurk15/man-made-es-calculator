import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json
import os

# Універсум значень для кількості навчань
training_count = np.arange(0, 11, 1)

# Визначення нечітких множин для кількості навчань
low = fuzz.trapmf(training_count, [0, 0, 2, 4])        # Низька кількість
moderate = fuzz.trimf(training_count, [3, 5, 7])      # Помірна кількість
high = fuzz.trapmf(training_count, [6, 8, 10, 10])    # Висока кількість

# Функція для визначення категорії на основі нечіткої логіки
def get_training_category(value):
    low_value = fuzz.interp_membership(training_count, low, value)
    moderate_value = fuzz.interp_membership(training_count, moderate, value)
    high_value = fuzz.interp_membership(training_count, high, value)

    # Ступені належності
    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    # Визначення категорії з найвищим ступенем належності
    return max(memberships, key=memberships.get)

# Генерація даних для всіх значень
data = [{"value": value, "category": get_training_category(value)} for value in range(11)]

# Створення директорії, якщо її немає
output_dir = "categories-data"
os.makedirs(output_dir, exist_ok=True)

# Запис результатів у JSON-файл
output_path = os.path.join(output_dir, "training_count.json")
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

# Візуалізація нечітких множин
plt.figure(figsize=(10, 6))
plt.plot(training_count, low, label="Low (Низька кількість навчань)", color="blue")
plt.plot(training_count, moderate, label="Moderate (Помірна кількість навчань)", color="green")
plt.plot(training_count, high, label="High (Висока кількість навчань)", color="orange")

# Додавання підписів і легенди
plt.title("Нечіткі множини для кількості навчань", fontsize=14)
plt.xlabel("Кількість навчань", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
