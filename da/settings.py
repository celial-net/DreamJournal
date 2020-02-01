import json
import os


# Class used for getting settings from the settings.json file
class Settings:
    @staticmethod
    def get(*keys):
        with open(os.path.dirname(os.path.realpath(__file__)) + '/../config/settings.json') as settings_file:
            settings = json.load(settings_file)
            for key in keys:
                if key in settings:
                    settings = settings[key]
            return settings
