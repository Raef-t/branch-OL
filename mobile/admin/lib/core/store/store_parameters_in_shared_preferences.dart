import 'package:shared_preferences/shared_preferences.dart';

class StoreParametersInSharedPreferences {
  static Future<void> saveIntParameter({
    required int intValue,
    required String key,
  }) async {
    final sharedPrefrences = await SharedPreferences.getInstance();
    await sharedPrefrences.setInt(key, intValue);
  }

  static Future<int?> getIntParameter({required String key}) async {
    final sharedPrefrences = await SharedPreferences.getInstance();
    return sharedPrefrences.getInt(key) ?? 0;
  }

  static Future<void> saveStringParameter({
    required String stringValue,
    required String key,
  }) async {
    final sharedPrefrences = await SharedPreferences.getInstance();
    await sharedPrefrences.setString(key, stringValue);
  }

  static Future<String?> getStringParameter({required String key}) async {
    final sharedPrefrences = await SharedPreferences.getInstance();
    return sharedPrefrences.getString(key) ?? '';
  }
}
