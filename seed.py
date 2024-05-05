import math

import requests
import random
from collections import defaultdict
import mysql.connector
from mysql.connector import Error

# base URL de l'API de ConceptNet
BASE_URL = "http://api.conceptnet.io"

def fetch_facts(relation, language, limit=1000):
    """ Récupérer les faits pour une relation donnée """
    url = f"{BASE_URL}/query?rel=/r/{relation}&limit={limit}&start=/c/{language}&end=/c/{language}"
    response = requests.get(url)
    if response.status_code == 200:
        return response.json().get('edges', [])
    return []

def collect_data():
    relations = [
        "RelatedTo", "FormOf", "IsA", "PartOf", "HasA",
        "UsedFor", "CapableOf", "AtLocation", "Causes",
        "HasSubevent", "HasFirstSubevent", "HasLastSubevent",
        "HasPrerequisite", "HasProperty", "MotivatedByGoal",
        "ObstructedBy", "Desires", "CreatedBy", "Synonym",
        "Antonym", "DerivedFrom", "SymbolOf", "DefinedAs"
    ]

    languages = ["en"]
    limit_per_language = 50

    facts = []
    concepts = set()
    seen_relations = set()

    while len(facts) < 1 or len(concepts) < 40 or len(seen_relations) < 10:
        relation = random.choice(relations)
        fetched_facts = []
        for lang in languages:
            fetched_facts.extend(fetch_facts(relation, lang, limit_per_language))

        for fact in fetched_facts:
            start_id = fact['start']['@id']
            start = fact['start']['label']
            end = fact['end']['label']
            rel = fact['rel']['label']
            if len(facts) < 1 or start not in concepts or end not in concepts or rel not in seen_relations:
                facts.append((start_id, start, rel, end))
                concepts.update([start])
                seen_relations.add(rel)
                if len(facts) >= 1 and len(concepts) >= 40 and len(seen_relations) >= 10:
                    break
    return facts, concepts, seen_relations

def facts_to_html(facts):
    html = "<table border='1'>\n"
    html += ("<tr><th id='start_id'>Start_id</th><th id='start'>Start</th>"
             "<th id='relation'>Relation</th><th id='end'>End</th></tr>\n")
    for start_id, start, relation, end in facts:
        html += f"<tr><td>{start_id}</td><td>{start}</td><td>{relation}</td><td>{end}</td></tr>\n"
    html += "</table>"
    return html

def main():
    try:
        file_location = "./backup/facts_table.html"
        facts, concepts, seen_relations = collect_data()
        html_file = open(file_location, "w")
        html_file.write(facts_to_html(facts))
        html_file.close()
        print(f"Le fichier {file_location} a été créé avec succès.")
    except Error as err:
        print(f"Error: {err}")

if __name__ == "__main__":
    main()
