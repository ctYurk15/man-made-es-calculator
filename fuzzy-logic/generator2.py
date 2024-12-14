import numpy as np
import skfuzzy as fuzz
import json

# Створюємо універсум для ймовірності
probability = np.arange(0, 101, 1)

# Визначаємо нечіткі множини
low = fuzz.trapmf(probability, [0, 0, 20, 40])        # Низька
moderate = fuzz.trimf(probability, [20, 50, 80])     # Помірна
high = fuzz.trapmf(probability, [60, 80, 100, 100])  # Висока
critical = fuzz.trapmf(probability, [90, 100, 100, 100])  # Критична

# Функція для визначення текстового значення на основі нечіткої логіки
def get_textual_category(prob):
    low_value = fuzz.interp_membership(probability, low, prob)
    moderate_value = fuzz.interp_membership(probability, moderate, prob)
    high_value = fuzz.interp_membership(probability, high, prob)
    critical_value = fuzz.interp_membership(probability, critical, prob)

    # Визначаємо максимальну належність
    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value,
        "critical": critical_value
    }

    return max(memberships, key=memberships.get)

# Генеруємо дані для всіх значень ймовірності
data = [{"value": prob, "category": get_textual_category(prob)} for prob in range(101)]

# Зберігаємо у JSON
with open("categories-data/es_probability.json", "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)
