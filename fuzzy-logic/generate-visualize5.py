import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json
import os

# Універсум значень для відсотка атестації
certification = np.arange(0, 101, 1)

# Визначення нечітких множин для відсотка атестації
low = fuzz.trapmf(certification, [0, 0, 30, 50])        # Низький відсоток атестації
moderate = fuzz.trimf(certification, [40, 60, 80])      # Помірний відсоток атестації
high = fuzz.trapmf(certification, [70, 90, 100, 100])   # Високий відсоток атестації

# Функція для визначення категорії на основі нечіткої логіки
def get_certification_category(value):
    low_value = fuzz.interp_membership(certification, low, value)
    moderate_value = fuzz.interp_membership(certification, moderate, value)
    high_value = fuzz.interp_membership(certification, high, value)

    # Ступені належності
    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    # Визначення категорії з найвищим ступенем належності
    return max(memberships, key=memberships.get)

# Генерація даних для всіх значень
data = [{"value": value, "category": get_certification_category(value)} for value in range(101)]

# Створення директорії, якщо її немає
output_dir = "categories-data"
os.makedirs(output_dir, exist_ok=True)

# Запис результатів у JSON-файл
output_path = os.path.join(output_dir, "certified_employees.json")
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

# Візуалізація нечітких множин
plt.figure(figsize=(10, 6))
plt.plot(certification, low, label="Low (Низький відсоток атестації)", color="blue")
plt.plot(certification, moderate, label="Moderate (Помірний відсоток атестації)", color="green")
plt.plot(certification, high, label="High (Високий відсоток атестації)", color="orange")

# Додавання підписів і легенди
plt.title("Нечіткі множини для відсотка атестації працівників", fontsize=14)
plt.xlabel("Відсоток атестації (%)", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
