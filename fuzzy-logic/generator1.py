import numpy as np
import json
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import sys

# Створюємо нечітку логіку
equipment_wear = ctrl.Antecedent(np.arange(0, 101, 1), 'equipment_wear')
maintenance_freq = ctrl.Antecedent(np.arange(0, 13, 1), 'maintenance_freq')
last_inspection = ctrl.Antecedent(np.arange(0, 13, 1), 'last_inspection')
trainings_count = ctrl.Antecedent(np.arange(0, 11, 1), 'trainings_count')
certification_rate = ctrl.Antecedent(np.arange(0, 101, 1), 'certification_rate')
knowledge_score = ctrl.Antecedent(np.arange(0, 101, 1), 'knowledge_score')
risk = ctrl.Consequent(np.arange(0, 101, 1), 'risk')

# Розширені функції належності
equipment_wear['poor'] = fuzz.trapmf(equipment_wear.universe, [0, 0, 20, 40])
equipment_wear['average'] = fuzz.trimf(equipment_wear.universe, [30, 50, 70])
equipment_wear['good'] = fuzz.trapmf(equipment_wear.universe, [60, 80, 100, 100])

risk['poor'] = fuzz.trapmf(risk.universe, [0, 0, 25, 50])
risk['average'] = fuzz.trimf(risk.universe, [25, 50, 75])
risk['good'] = fuzz.trapmf(risk.universe, [50, 75, 100, 100])

maintenance_freq['poor'] = fuzz.trapmf(maintenance_freq.universe, [0, 0, 3, 6])
maintenance_freq['average'] = fuzz.trimf(maintenance_freq.universe, [4, 6, 8])
maintenance_freq['good'] = fuzz.trapmf(maintenance_freq.universe, [6, 9, 12, 12])

last_inspection['poor'] = fuzz.trapmf(last_inspection.universe, [0, 0, 3, 6])
last_inspection['average'] = fuzz.trimf(last_inspection.universe, [4, 6, 8])
last_inspection['good'] = fuzz.trapmf(last_inspection.universe, [6, 9, 12, 12])

trainings_count['poor'] = fuzz.trapmf(trainings_count.universe, [0, 0, 2, 4])
trainings_count['average'] = fuzz.trimf(trainings_count.universe, [3, 5, 7])
trainings_count['good'] = fuzz.trapmf(trainings_count.universe, [6, 8, 10, 10])

certification_rate['poor'] = fuzz.trapmf(certification_rate.universe, [0, 0, 40, 60])
certification_rate['average'] = fuzz.trimf(certification_rate.universe, [50, 70, 90])
certification_rate['good'] = fuzz.trapmf(certification_rate.universe, [80, 90, 100, 100])

knowledge_score['poor'] = fuzz.trapmf(knowledge_score.universe, [0, 0, 40, 60])
knowledge_score['average'] = fuzz.trimf(knowledge_score.universe, [50, 70, 90])
knowledge_score['good'] = fuzz.trapmf(knowledge_score.universe, [80, 90, 100, 100])

risk['poor'] = fuzz.trapmf(risk.universe, [0, 0, 25, 50])
risk['average'] = fuzz.trimf(risk.universe, [25, 50, 75])
risk['good'] = fuzz.trapmf(risk.universe, [50, 75, 100, 100])


# Правила
rule1 = ctrl.Rule(equipment_wear['poor'] & maintenance_freq['poor'] & last_inspection['poor'], risk['good'])
rule2 = ctrl.Rule(equipment_wear['good'] & trainings_count['good'] & certification_rate['good'] & knowledge_score['good'], risk['poor'])
rule3 = ctrl.Rule(trainings_count['poor'] | certification_rate['poor'] | knowledge_score['poor'], risk['good'])

# Додаткове правило для крайніх значень
rule_boundary = ctrl.Rule(equipment_wear['poor'] & maintenance_freq['poor'], risk['average'])
rule_boundary2 = ctrl.Rule(equipment_wear['poor'] & maintenance_freq['average'], risk['average'])

# Система контролю
risk_ctrl = ctrl.ControlSystem([rule1, rule2, rule3, rule_boundary])
risk_simulation = ctrl.ControlSystemSimulation(risk_ctrl)

# Генеруємо дані
data = []
for equipment in range(0, 101, 10):  # Кроки 10%
    for maintenance in range(0, 13, 2):  # Кроки 2
        for inspection in range(0, 13, 2):
            for training in range(0, 11, 2):
                for certification in range(0, 101, 20):  # Кроки 20%
                    for knowledge in range(0, 101, 20):
                        try:
                            # Вхідні дані
                            risk_simulation.input['equipment_wear'] = equipment
                            risk_simulation.input['maintenance_freq'] = maintenance
                            risk_simulation.input['last_inspection'] = inspection
                            risk_simulation.input['trainings_count'] = training
                            risk_simulation.input['certification_rate'] = certification
                            risk_simulation.input['knowledge_score'] = knowledge
                            
                            # Розрахунок
                            risk_simulation.compute()
                            print(f"Ризик для {equipment=}, {maintenance=}, {inspection=}: {risk_simulation.output['risk']}")
                            
                            # Збереження результату
                            data.append({
                                "equipment_wear": equipment,
                                "maintenance_freq": maintenance,
                                "last_inspection": inspection,
                                "trainings_count": training,
                                "certification_rate": certification,
                                "knowledge_score": knowledge,
                                "risk": round(risk_simulation.output['risk'], 2)
                            })
                        except Exception as e:
                            print(f"Помилка обчислення для {equipment=}, {maintenance=}, {inspection=}: {e}")
                            exit()

# Запис у JSON
with open("risk_data.json", "w") as f:
    json.dump(data, f, indent=4)
