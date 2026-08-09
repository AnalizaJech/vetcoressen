import json

with open('C:/Users/JORGE/.gemini/antigravity-ide/brain/26c51f2a-f57d-4db8-9823-280c7334f788/.system_generated/logs/transcript_full.jsonl', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
            if 'tool_calls' in data and len(data['tool_calls']) > 0:
                args = data['tool_calls'][0].get('args', {})
                if data['tool_calls'][0].get('name') == 'write_to_file' and 'historia-clinica-index.blade.php' in args.get('TargetFile', ''):
                    content = args.get('CodeContent', '')
                    # CodeContent is a string representation of the code, so it might need to be evaluated if it's JSON encoded, but here json.loads already parsed the outer JSON
                    with open('C:/Users/JORGE/Desktop/vetcoressen/resources/views/livewire/historias-clinicas/historia-clinica-index.blade.php', 'w', encoding='utf-8') as out:
                        out.write(content)
                    print("Wrote file.")
        except Exception as e:
            pass
