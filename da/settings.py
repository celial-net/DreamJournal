import json


# Class used for getting settings from the settings.json file
class Settings:
    @staticmethod
    def get(self, *keys):
        with open('../config/settings.json') as settings_file:
            settings = json.load(settings_file)
            for key in keys:
                settings = settings[key] or None
                if settings is None:
                    return None
            return settings
