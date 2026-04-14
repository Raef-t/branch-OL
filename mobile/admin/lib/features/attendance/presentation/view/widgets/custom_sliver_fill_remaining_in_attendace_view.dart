import 'package:flutter/cupertino.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/attendance/presentation/view/widgets/custom_bottom_section_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_header_section_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_image_with_text_in_attendance_view.dart';

class CustomSliverFillRemainingInAttendaceView extends StatelessWidget {
  const CustomSliverFillRemainingInAttendaceView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height17(context: context),
            const CustomHeaderSectionInAttendaceView(),
            Heights.height26(context: context),
            const CustomImageWithTextInAttendanceView(),
            Heights.height17(context: context),
            const CustomBottomSectionInAttendaceView(),
          ],
        ),
      ),
    );
  }
}
