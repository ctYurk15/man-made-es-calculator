import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json

knowledge = np.arange(0, 101, 1)

low = fuzz.trapmf(knowledge, [0, 0, 20, 40])         # Низький рівень
moderate = fuzz.trimf(knowledge, [20, 50, 80])       # Помірний рівень
high = fuzz.trapmf(knowledge, [60, 80, 100, 100])    # Високий рівень

def get_knowledge_category(value):
    low_value = fuzz.interp_membership(knowledge, low, value)
    moderate_value = fuzz.interp_membership(knowledge, moderate, value)
    high_value = fuzz.interp_membership(knowledge, high, value)

    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    return max(memberships, key=memberships.get)

data = [{"value": value, "category": get_knowledge_category(value)} for value in range(101)]

with open("categories-data/knowledge_score.json", "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)


plt.figure(figsize=(10, 6))
plt.plot(knowledge, low, label="Low (Низький рівень)", color="blue")
plt.plot(knowledge, moderate, label="Moderate (Помірний рівень)", color="green")
plt.plot(knowledge, high, label="High (Високий рівень)", color="orange")

plt.title("Нечіткі множини для рівня знань", fontsize=14)
plt.xlabel("Рівень знань (%)", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
