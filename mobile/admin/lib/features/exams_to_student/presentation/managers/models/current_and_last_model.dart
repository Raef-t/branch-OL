import 'today_model.dart';
import 'current_model.dart';

class CurrentAndLastModel {
  final List<CurrentModel> currentList;
  final List<LastModel> lastList;

  CurrentAndLastModel({required this.currentList, required this.lastList});

  factory CurrentAndLastModel.fromJson({required Map<String, dynamic> json}) {
    return CurrentAndLastModel(
      currentList: (json['today'] as List)
          .map((e) => CurrentModel.fromJson(json: e))
          .toList(),
      lastList: (json['current_week'] as List)
          .map((e) => LastModel.fromJson(json: e))
          .toList(),
    );
  }
}
