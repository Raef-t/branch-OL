class BatchAverageModel {
  final String? batchName;
  double? rating;
  BatchAverageModel({required this.batchName, required this.rating});
  factory BatchAverageModel.fromJson({required Map<String, dynamic> json}) {
    return BatchAverageModel(
      batchName: json['batch_name'] as String?,
      rating: json['overall_average_marks'] != null
          ? (json['overall_average_marks'] as num).toDouble()
          : null,
    );
    //rating attribute i need it type double(because LineChart rating work with double), but i can't determined on json all time return to me double because sometimes will return int, so i will receive the attribute from json and i tell it it's as num(type your value like this type(this is meaning of as), and after all that i parse the value attribute to double)
  }
}
