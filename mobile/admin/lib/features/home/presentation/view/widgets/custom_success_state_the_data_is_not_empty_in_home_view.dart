import 'dart:async';

import 'package:flutter/widgets.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/home/presentation/view/widgets/batch_name_tooltip_widget.dart';
import '/features/home/presentation/view/widgets/custom_circle_with_check_and_text_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_two_circle_avatars_with_text_in_home_view.dart';

class CustomSuccessStateTheDataIsNotEmptyInHomeView extends StatefulWidget {
  const CustomSuccessStateTheDataIsNotEmptyInHomeView({
    super.key,
    required this.length,
    required this.examsModelList,
  });
  final int length;
  final List<ExamsModel> examsModelList;

  @override
  State<CustomSuccessStateTheDataIsNotEmptyInHomeView> createState() =>
      _CustomSuccessStateTheDataIsNotEmptyInHomeViewState();
}

class _CustomSuccessStateTheDataIsNotEmptyInHomeViewState
    extends State<CustomSuccessStateTheDataIsNotEmptyInHomeView> {
  String? _selectedName;
  Timer? _tooltipTimer;

  void _showTooltip(String name) {
    _tooltipTimer?.cancel();
    setState(() => _selectedName = name);
    _tooltipTimer = Timer(const Duration(seconds: 1), () {
      if (mounted) setState(() => _selectedName = null);
    });
  }

  @override
  void dispose() {
    _tooltipTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          reverse: true,
          child: Row(
            textDirection: TextDirection.rtl,
            children: List.generate(widget.length, (index) {
              final examsModel = widget.examsModelList[index];
              final subjectName =
                  examsModel.name ??
                  (examsModel
                          .batchSubjectModel
                          ?.instructorSubjectModel
                          ?.subjectModel
                          ?.subjectName ??
                      examsModel.examContent) ??
                  '';
              final child = examsModel.isChecked
                  ? CustomCircleWithCheckAndTextInHomeView(
                      name: subjectName,
                      onTap: () => _showTooltip(subjectName),
                    )
                  : CustomTwoCircleAvatarsWithTextInHomeView(
                      number: index + 1,
                      name: subjectName,
                      onTap: () => _showTooltip(subjectName),
                    );
              return OnlyPaddingWithChild.left30(context: context, child: child);
            }),
          ),
        ),
        if (_selectedName != null)
          BatchNameTooltipWidget(batchName: _selectedName!),
      ],
    );
  }
}
